package com.townwizard.db.util;

import com.townwizard.db.logger.Log;

public final class ExceptionHandler {

    private ExceptionHandler() {}
    
    public static void handle(Exception e) {
        Log.exception(e);
    }
}