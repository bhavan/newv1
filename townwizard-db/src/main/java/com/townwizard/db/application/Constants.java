package com.townwizard.db.application;

import java.util.HashMap;
import java.util.Map;

public final class Constants {
    public static final String CONTEXT_PATH = "/tw";
    public static final String RESOURCES_PACKAGE = "com.townwizard.db.resources";
    public static final Map<String, Object> RESOURCE_FEATURES = new HashMap<>();
    static {
        RESOURCE_FEATURES.put("com.sun.jersey.api.json.POJOMappingFeature", true);
    }
}