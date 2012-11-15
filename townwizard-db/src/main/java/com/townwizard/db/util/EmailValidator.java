package com.townwizard.db.util;

import javax.mail.internet.AddressException;
import javax.mail.internet.InternetAddress;

public final class EmailValidator {
    
    private EmailValidator() {}

    public static boolean isValidEmailAddress(String email) {
        if(email == null || email.isEmpty()) return false;
        
        boolean result = true;
        try {
            InternetAddress emailAddr = new InternetAddress(email);
            emailAddr.validate();
        } catch (AddressException ex) {
            result = false;
        }
        return result;
    }
    
}