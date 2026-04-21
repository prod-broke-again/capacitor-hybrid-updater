package com.hybrid.updater

import android.content.Context
import java.io.File
import java.io.FileOutputStream
import java.io.IOException
import java.util.zip.ZipEntry
import java.util.zip.ZipInputStream

internal class BundleRuntime(private val context: Context, private val stateStore: RuntimeStateStore) {
    private val rootDir: File = File(context.filesDir, "hybrid-updater-bundles").also { it.mkdirs() }

    fun getState(): RuntimeState = stateStore.read()

    @Throws(IOException::class)
    fun extractBundle(zipFile: File, version: String): File {
        val targetDir = File(rootDir, "bundle-$version")
        if (targetDir.exists()) {
            targetDir.deleteRecursively()
        }
        targetDir.mkdirs()

        ZipInputStream(zipFile.inputStream()).use { zip ->
            var entry: ZipEntry? = zip.nextEntry
            while (entry != null) {
                val safePath = sanitizeEntryPath(targetDir, entry.name)
                if (entry.isDirectory) {
                    safePath.mkdirs()
                } else {
                    safePath.parentFile?.mkdirs()
                    FileOutputStream(safePath).use { output ->
                        zip.copyTo(output)
                    }
                }
                zip.closeEntry()
                entry = zip.nextEntry
            }
        }

        val indexFile = File(targetDir, "index.html")
        if (!indexFile.exists()) {
            throw IOException("Extracted bundle does not contain index.html at root.")
        }

        return targetDir
    }

    fun applyNext(version: String, bundleDir: File) {
        stateStore.setNext(version, bundleDir.absolutePath)
    }

    fun activateCurrent(version: String, bundleDir: File) {
        stateStore.setCurrent(version, bundleDir.absolutePath)
    }

    fun rollbackToBuiltin() {
        stateStore.clearToBuiltin()
    }

    fun cleanupOldBundles(retainPath: String?) {
        val entries = rootDir.listFiles() ?: return
        for (entry in entries) {
            if (retainPath == null || entry.absolutePath != retainPath) {
                entry.deleteRecursively()
            }
        }
    }

    private fun sanitizeEntryPath(targetDir: File, entryName: String): File {
        val output = File(targetDir, entryName)
        val targetPath = output.canonicalPath
        val rootPath = targetDir.canonicalPath
        if (!targetPath.startsWith(rootPath + File.separator)) {
            throw IOException("ZIP entry path traversal detected: $entryName")
        }
        return output
    }
}
