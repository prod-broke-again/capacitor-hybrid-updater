package com.hybrid.updater

import android.content.Context

internal data class RuntimeState(
    val currentBundleVersion: String,
    val currentBundlePath: String?,
    val nextBundleVersion: String?,
    val nextBundlePath: String?
)

internal class RuntimeStateStore(context: Context) {
    private val prefs = context.getSharedPreferences("hybrid_updater_runtime", Context.MODE_PRIVATE)

    fun read(): RuntimeState {
        return RuntimeState(
            currentBundleVersion = prefs.getString("current_bundle_version", "builtin") ?: "builtin",
            currentBundlePath = prefs.getString("current_bundle_path", null),
            nextBundleVersion = prefs.getString("next_bundle_version", null),
            nextBundlePath = prefs.getString("next_bundle_path", null)
        )
    }

    fun setCurrent(version: String, path: String?) {
        prefs.edit()
            .putString("current_bundle_version", version)
            .putString("current_bundle_path", path)
            .remove("next_bundle_version")
            .remove("next_bundle_path")
            .apply()
    }

    fun setNext(version: String, path: String) {
        prefs.edit()
            .putString("next_bundle_version", version)
            .putString("next_bundle_path", path)
            .apply()
    }

    fun clearToBuiltin() {
        prefs.edit()
            .putString("current_bundle_version", "builtin")
            .remove("current_bundle_path")
            .remove("next_bundle_version")
            .remove("next_bundle_path")
            .apply()
    }
}
