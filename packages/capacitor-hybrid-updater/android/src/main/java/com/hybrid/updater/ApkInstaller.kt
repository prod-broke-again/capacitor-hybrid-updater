package com.hybrid.updater

import android.content.ActivityNotFoundException
import android.content.Context
import android.content.Intent
import android.net.Uri
import androidx.core.content.FileProvider
import java.io.File
import java.io.FileOutputStream
import java.io.IOException
import java.net.HttpURLConnection
import java.net.URL

internal class ApkInstaller(private val context: Context) {
    @Throws(IOException::class)
    fun downloadApk(
        sourceUrl: String,
        expectedChecksum: String?,
        onProgress: (progress: Int) -> Unit
    ): File {
        val targetFile = File(context.cacheDir, "apk-update-${System.currentTimeMillis()}.apk")
        val connection = URL(sourceUrl).openConnection() as HttpURLConnection
        connection.requestMethod = "GET"
        connection.connectTimeout = 15000
        connection.readTimeout = 60000
        connection.connect()

        if (connection.responseCode !in 200..299) {
            throw IOException("Failed to download APK: HTTP ${connection.responseCode}")
        }

        val totalBytes = connection.contentLengthLong
        var copiedBytes = 0L
        connection.inputStream.use { input ->
            FileOutputStream(targetFile).use { output ->
                val buffer = ByteArray(8192)
                while (true) {
                    val read = input.read(buffer)
                    if (read <= 0) {
                        break
                    }
                    output.write(buffer, 0, read)
                    copiedBytes += read.toLong()
                    if (totalBytes > 0) {
                        val progress = ((copiedBytes * 100L) / totalBytes).toInt().coerceIn(0, 100)
                        onProgress(progress)
                    }
                }
            }
        }
        connection.disconnect()

        if (!ChecksumVerifier.matches(targetFile, expectedChecksum)) {
            targetFile.delete()
            throw IOException("APK checksum mismatch.")
        }
        return targetFile
    }

    fun launchInstall(apkFile: File): Boolean {
        val fileUri: Uri = FileProvider.getUriForFile(
            context,
            "${context.packageName}.fileprovider",
            apkFile
        )
        val intent = Intent(Intent.ACTION_VIEW).apply {
            setDataAndType(fileUri, "application/vnd.android.package-archive")
            flags = Intent.FLAG_GRANT_READ_URI_PERMISSION or Intent.FLAG_ACTIVITY_NEW_TASK
        }

        return try {
            context.startActivity(intent)
            true
        } catch (_: ActivityNotFoundException) {
            false
        }
    }
}
