package com.townwizard.db.test;

import java.util.Properties;

import org.hibernate.SessionFactory;
import org.hibernate.cfg.Configuration;
import org.hibernate.cfg.ImprovedNamingStrategy;
import org.hibernate.service.ServiceRegistry;
import org.hibernate.service.ServiceRegistryBuilder;
import org.junit.AfterClass;
import org.junit.BeforeClass;

public abstract class TestSupport {
    
    private static Properties properties;
    private static SessionFactory sessionFactory;
    
    @BeforeClass
    public static void beforeTestsRun() throws Exception {
        properties = new Properties();
        properties.load(ClassLoader.getSystemResourceAsStream("test.properties"));
        sessionFactory = initSessionFactory();
    }
    
    @AfterClass
    public static void afterTestsRun() throws Exception {
        if(sessionFactory != null) {
            sessionFactory.close();
        }
    }
    
    protected String getWebServicesUrlBase() {
        return properties.getProperty("web_services_url_base");
    }
    
    protected SessionFactory getSessionFactory() {
        return sessionFactory;
    }
    
    private static SessionFactory initSessionFactory() {
        Configuration configuration = new Configuration();
        configuration.configure();
        configuration.setNamingStrategy(ImprovedNamingStrategy.INSTANCE);
        ServiceRegistry serviceRegistry = new ServiceRegistryBuilder().
                applySettings(configuration.getProperties()).buildServiceRegistry();
        return configuration.buildSessionFactory(serviceRegistry);
    }
}