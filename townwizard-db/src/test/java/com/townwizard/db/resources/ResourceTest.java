package com.townwizard.db.resources;

import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;

import com.townwizard.db.test.TestSupport;

public abstract class ResourceTest extends TestSupport {    

    protected StatusLine executePostRequest(String path, String entity, String contentType) {
        StatusLine statusLine = null;
        try {
            HttpClient c = new DefaultHttpClient();
            HttpPost post = new HttpPost(getWebServicesUrlBase() + "/users");
            post.setEntity(new StringEntity(entity));
            post.setHeader("Content-Type", contentType);
            HttpResponse response = c.execute(post);
            statusLine = response.getStatusLine();
        } catch(Throwable e) {
            if(statusLine != null) {
                System.out.println(statusLine);
            }
            e.printStackTrace();
        }
        return statusLine;
    }
    
    protected StatusLine executePostJSONRequest(String path, String entity) {
        return executePostRequest(path, entity, "application/json");
    }
    
}