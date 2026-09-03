package com.pbqueue.app;

import android.annotation.SuppressLint;
import android.os.Bundle;
import android.speech.tts.TextToSpeech;
import android.webkit.JavascriptInterface;
import android.webkit.WebSettings;
import android.webkit.WebView;

import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {
    private TextToSpeech textToSpeech;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        WebView webView = new WebView(this);
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setAllowFileAccess(true);
        textToSpeech = new TextToSpeech(this, status -> {
            if (status == TextToSpeech.SUCCESS) {
                textToSpeech.setLanguage(java.util.Locale.US);
            }
        });
        webView.addJavascriptInterface(new VoiceBridge(), "Android");
        webView.loadUrl("file:///android_asset/public/index.html");
        setContentView(webView);
    }

    private class VoiceBridge {
        @JavascriptInterface
        public void speak(String message) {
            runOnUiThread(() -> {
                if (textToSpeech != null) {
                    textToSpeech.speak(message, TextToSpeech.QUEUE_FLUSH, null, "court-call");
                }
            });
        }
    }

    @Override
    protected void onDestroy() {
        if (textToSpeech != null) {
            textToSpeech.stop();
            textToSpeech.shutdown();
        }
        super.onDestroy();
    }
}
