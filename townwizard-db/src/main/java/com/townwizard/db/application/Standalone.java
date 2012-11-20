package com.townwizard.db.application;

import java.net.URI;
import java.util.HashMap;
import java.util.Map;

import javax.ws.rs.core.UriBuilder;

import org.glassfish.grizzly.http.server.HttpServer;
import org.springframework.context.ConfigurableApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

import com.sun.jersey.api.container.grizzly2.GrizzlyServerFactory;
import com.sun.jersey.api.core.PackagesResourceConfig;
import com.sun.jersey.api.core.ResourceConfig;
import com.sun.jersey.core.spi.component.ioc.IoCComponentProviderFactory;
import com.sun.jersey.spi.spring.container.SpringComponentProviderFactory;
import com.townwizard.db.logger.Log;

/**
 * Starts standalone server
 */
public class Standalone {    
   
    private static URI BASE_URI = UriBuilder.fromUri("http://localhost/tw").port(8080).build();
    private static final String RESOURCES_PACKAGE = "com.townwizard.db.resources";
    private static final Map<String, Object> RESOURCE_FEATURES = new HashMap<>();
    static {
        RESOURCE_FEATURES.put("com.sun.jersey.api.json.POJOMappingFeature", true);
    }
    
    public static void main(String[] args) {
        HttpServer server = null;
        try {
            Log.info("Starting grizzly...");
            server = startServer();
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
    
    public static HttpServer startServer() throws Exception {
        ResourceConfig rc = new PackagesResourceConfig(RESOURCES_PACKAGE);
        rc.setPropertiesAndFeatures(RESOURCE_FEATURES);

        ConfigurableApplicationContext springContext = new ClassPathXmlApplicationContext(
                new String[] { "application.xml" });

        IoCComponentProviderFactory componentProviderFactory = 
                new SpringComponentProviderFactory(rc, springContext);

        return GrizzlyServerFactory.createHttpServer(BASE_URI, rc, componentProviderFactory);
    }
}