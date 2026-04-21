package com.hybrid.updater

import java.io.File
import java.io.FileInputStream
import java.security.MessageDigest

internal object ChecksumVerifier {
    fun sha256(file: File): String {
        val digest = MessageDigest.getInstance("SHA-256")
        FileInputStream(file).use { input ->
            val buffer = ByteArray(8192)
            while (true) {
                val read = input.read(buffer)
                if (read <= 0) {
                    break
                }
                digest.update(buffer, 0, read)
            }
        }
        return digest.digest().joinToString("") { value -> "%02x".format(value) }
    }

    fun matches(file: File, expected: String?): Boolean {
        if (expected.isNullOrBlank()) {
            return true
        }
        return sha256(file).equals(expected, ignoreCase = true)
    }
}
