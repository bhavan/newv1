package com.townwizard.db.util;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.StringWriter;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

public final class HttpUtils {
    
    private HttpUtils(){}
    
    public static String executeGetRequest(String path) {
        try {
            HttpClient c = new DefaultHttpClient();
            HttpGet get = new HttpGet(path);
            HttpResponse response = c.execute(get);
            String result = copyToString(response.getEntity().getContent());
            return result;
        } catch (Throwable e) {
            e.printStackTrace();
            return null;
        }
    }
    
    private static String copyToString(InputStream is) throws IOException {
        BufferedReader in = new BufferedReader(new InputStreamReader(is));
        StringWriter out = new StringWriter();
        String s;
        while((s = in.readLine()) != null) {
            out.append(s);
        }
        return out.toString();
    }

}
