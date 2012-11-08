package com.townwizard.db.application;

import java.net.URI;

import javax.ws.rs.core.UriBuilder;

import org.glassfish.grizzly.http.server.HttpServer;

import com.sun.jersey.api.container.grizzly2.GrizzlyServerFactory;
import com.sun.jersey.api.core.PackagesResourceConfig;
import com.sun.jersey.api.core.ResourceConfig;

import com.townwizard.db.logger.Log;

/**
 * Starts standalone server
 */
public class Standalone {
    
    private static URI BASE_URI = UriBuilder.fromUri("http://localhost" + Constants.CONTEXT_PATH).port(9998).build();
    
    public static void main(String[] args) {
        startServer();
    }
    
    private static void startServer() {
        HttpServer server = null;
        try {
            Log.info("Starting grizzly...");
            ResourceConfig rc = new PackagesResourceConfig(Constants.RESOURCES_PACKAGE);
            rc.setPropertiesAndFeatures(Constants.RESOURCE_FEATURES);
            server = GrizzlyServerFactory.createHttpServer(BASE_URI, rc);
            Log.info("Press any key to stop the server...");
            System.in.read();
        } catch (Exception e) {
            if(server != null) {
                server.stop();
            }
            e.printStackTrace();
            Log.exception(e);  
        }
    }
}