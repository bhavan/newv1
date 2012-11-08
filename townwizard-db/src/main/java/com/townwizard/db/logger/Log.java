package com.townwizard.db.logger;

import java.util.logging.Level;
import java.util.logging.Logger;

public final class Log {
    
    private static Logger logger = Logger.getLogger("com.townwizard.db");

    public static boolean isInfoEnabled() {
        return logger.isLoggable(Level.INFO);
    } 
    
    public static boolean isDebugEnabled() {
        return logger.isLoggable(Level.FINE);
    }
    
    public static boolean isWarningEnabled() {
        return logger.isLoggable(Level.WARNING);
    }

    public static boolean isErrorEnabled() {
        return logger.isLoggable(Level.SEVERE);
    }
    
    public static void info(String message) {
        logger.info(message);
    }
    
    public static void debug(String message) {
        logger.fine(message);
    }
    
    public static void error(String message) {
        logger.severe(message);
    }
    
    public static void warning(String message) {
        logger.warning(message);
    }
    
    public static void exception(Throwable e) {
        if(isErrorEnabled()) {
            error(e.getMessage());
        }
    }
}
