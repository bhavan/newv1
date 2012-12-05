package com.townwizard.db.model;

import java.util.Date;

public class LoginRequest {
    private final String loginId;
    private final String location;
    private final Date created;
    
    public LoginRequest(String loginId, String location, Date created) {
        this.loginId = loginId;
        this.location = location;
        this.created = created;
    }

    public String getLoginId() {
        return loginId;
    }

    public String getLocation() {
        return location;
    }

    public Date getCreated() {
        return created;
    }
}