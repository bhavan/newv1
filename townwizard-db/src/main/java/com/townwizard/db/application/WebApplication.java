package com.townwizard.db.application;

import javax.ws.rs.ApplicationPath;

import com.sun.jersey.api.core.PackagesResourceConfig;
import com.townwizard.db.logger.Log;

@ApplicationPath("/")
public class WebApplication extends PackagesResourceConfig {
	public WebApplication() {	    
		super(Constants.RESOURCES_PACKAGE);
		setPropertiesAndFeatures(Constants.RESOURCE_FEATURES);
		Log.info("Starting Town Wizard web services...");
	}
}
