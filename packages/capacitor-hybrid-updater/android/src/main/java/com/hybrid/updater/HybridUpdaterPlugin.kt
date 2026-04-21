package com.hybrid.updater

import com.getcapacitor.JSObject
import com.getcapacitor.Plugin
import com.getcapacitor.PluginCall
import com.getcapacitor.PluginMethod
import com.getcapacitor.annotation.CapacitorPlugin
import java.io.File
import java.io.FileOutputStream
import java.io.IOException
import java.net.HttpURLConnection
import java.net.URL

@CapacitorPlugin(name = "HybridUpdater")
class HybridUpdaterPlugin : Plugin() {
    private lateinit var stateStore: RuntimeStateStore
    private lateinit var bundleRuntime: BundleRuntime
    private lateinit var apkInstaller: ApkInstaller

    override fun load() {
        super.load()
        stateStore = RuntimeStateStore(context)
        bundleRuntime = BundleRuntime(context, stateStore)
        apkInstaller = ApkInstaller(context)
        activatePendingBundleIfAny()
    }

    @PluginMethod
    fun configure(call: PluginCall) {
        call.resolve()
    }

    @PluginMethod
    fun getCurrentState(call: PluginCall) {
        val state = stateStore.read()
        val packageInfo = context.packageManager.getPackageInfo(context.packageName, 0)
        val versionName = packageInfo.versionName ?: "0.0.0"
        val versionCode = packageInfo.longVersionCode.toInt()
        val payload = JSObject().apply {
            put("provider", "laravel")
            put("channel", "stable")
            put("currentNativeVersion", versionName)
            put("currentNativeBuild", versionCode)
            put("currentWebBundleVersion", state.currentBundleVersion)
            put("activeWebBundlePath", state.currentBundlePath)
        }
        call.resolve(payload)
    }

    @PluginMethod
    fun downloadUpdate(call: PluginCall) {
        val manifest = call.getObject("manifest")
        val artifactType = call.getString("artifactType")
        val artifacts = manifest?.getJSONArray("artifacts")
        if (artifacts == null || artifactType.isNullOrBlank()) {
            call.reject("Manifest and artifactType are required.")
            return
        }

        var artifactUrl: String? = null
        var checksum: String? = null
        for (index in 0 until artifacts.length()) {
            val item = artifacts.getJSONObject(index)
            if (item.getString("type") == artifactType) {
                artifactUrl = item.getString("url")
                checksum = if (item.has("checksum")) item.getString("checksum") else null
                break
            }
        }

        if (artifactUrl == null) {
            call.reject("Artifact not found.")
            return
        }

        try {
            val file = downloadArtifact(artifactUrl, artifactType, checksum) { progress ->
                val event = JSObject().apply {
                    put("artifactType", artifactType)
                    put("progress", progress)
                }
                notifyListeners("onDownloadProgress", event)
            }
            val response = JSObject().apply {
                put("artifactType", artifactType)
                put("localPath", file.absolutePath)
                put("checksumVerified", true)
            }
            call.resolve(response)
        } catch (error: Exception) {
            emitError("RUNTIME_IO_ERROR", error.message ?: "Failed to download artifact.")
            call.reject(error.message)
        }
    }

    @PluginMethod
    fun applyWebUpdate(call: PluginCall) {
        val bundlePath = call.getString("bundlePath")
        val expectedVersion = call.getString("expectedVersion")
        if (bundlePath.isNullOrBlank() || expectedVersion.isNullOrBlank()) {
            call.reject("bundlePath and expectedVersion are required.")
            return
        }

        try {
            val zipFile = File(bundlePath)
            val extracted = bundleRuntime.extractBundle(zipFile, expectedVersion)
            bundleRuntime.applyNext(expectedVersion, extracted)
            val response = JSObject().apply {
                put("appliedVersion", expectedVersion)
                put("willActivateOnRestart", true)
            }
            call.resolve(response)
        } catch (error: Exception) {
            emitError("WEB_APPLY_FAILED", error.message ?: "Failed to apply web update.")
            call.reject(error.message)
        }
    }

    @PluginMethod
    fun downloadApk(call: PluginCall) {
        val manifest = call.getObject("manifest")
        val artifacts = manifest?.getJSONArray("artifacts")
        if (artifacts == null) {
            call.reject("Manifest is required.")
            return
        }

        var artifactUrl: String? = null
        var checksum: String? = null
        for (index in 0 until artifacts.length()) {
            val item = artifacts.getJSONObject(index)
            if (item.getString("type") == "apk") {
                artifactUrl = item.getString("url")
                checksum = if (item.has("checksum")) item.getString("checksum") else null
                break
            }
        }

        if (artifactUrl == null) {
            call.reject("APK artifact not found.")
            return
        }

        try {
            val file = apkInstaller.downloadApk(artifactUrl, checksum) { progress ->
                val event = JSObject().apply {
                    put("artifactType", "apk")
                    put("progress", progress)
                }
                notifyListeners("onDownloadProgress", event)
            }
            val response = JSObject().apply {
                put("artifactType", "apk")
                put("localPath", file.absolutePath)
                put("checksumVerified", true)
            }
            call.resolve(response)
        } catch (error: Exception) {
            emitError("APK_INSTALL_FAILED", error.message ?: "Failed to download APK.")
            call.reject(error.message)
        }
    }

    @PluginMethod
    fun installApk(call: PluginCall) {
        val apkPath = call.getString("apkPath")
        if (apkPath.isNullOrBlank()) {
            call.reject("apkPath is required.")
            return
        }

        val started = apkInstaller.launchInstall(File(apkPath))
        if (!started) {
            emitError("APK_INSTALL_FAILED", "Unable to start Android installer intent.")
        }
        call.resolve(JSObject().apply { put("started", started) })
    }

    @PluginMethod
    fun resetToBuiltin(call: PluginCall) {
        bundleRuntime.rollbackToBuiltin()
        bridge?.setServerAssetPath("public")
        call.resolve(JSObject().apply { put("reset", true) })
    }

    private fun activatePendingBundleIfAny() {
        val state = stateStore.read()
        val nextPath = state.nextBundlePath ?: return
        val nextVersion = state.nextBundleVersion ?: return
        val bundleDir = File(nextPath)
        if (!bundleDir.exists()) {
            stateStore.clearToBuiltin()
            return
        }

        bridge?.setServerBasePath(bundleDir.absolutePath)
        bundleRuntime.activateCurrent(nextVersion, bundleDir)
        bundleRuntime.cleanupOldBundles(bundleDir.absolutePath)
    }

    @Throws(IOException::class)
    private fun downloadArtifact(
        sourceUrl: String,
        artifactType: String,
        expectedChecksum: String?,
        onProgress: (Int) -> Unit
    ): File {
        val extension = if (artifactType == "apk") "apk" else "zip"
        val target = File(context.cacheDir, "$artifactType-${System.currentTimeMillis()}.$extension")
        val connection = URL(sourceUrl).openConnection() as HttpURLConnection
        connection.requestMethod = "GET"
        connection.connectTimeout = 15000
        connection.readTimeout = 60000
        connection.connect()
        if (connection.responseCode !in 200..299) {
            throw IOException("HTTP ${connection.responseCode} while downloading artifact.")
        }

        val contentLength = connection.contentLengthLong
        var copied = 0L
        connection.inputStream.use { input ->
            FileOutputStream(target).use { output ->
                val buffer = ByteArray(8192)
                while (true) {
                    val read = input.read(buffer)
                    if (read <= 0) {
                        break
                    }
                    output.write(buffer, 0, read)
                    copied += read.toLong()
                    if (contentLength > 0) {
                        onProgress(((copied * 100L) / contentLength).toInt().coerceIn(0, 100))
                    }
                }
            }
        }
        connection.disconnect()

        if (!ChecksumVerifier.matches(target, expectedChecksum)) {
            target.delete()
            throw IOException("Checksum mismatch for downloaded artifact.")
        }
        return target
    }

    private fun emitError(code: String, message: String) {
        val error = JSObject().apply {
            put("code", code)
            put("message", message)
        }
        notifyListeners("onError", error)
    }
}
