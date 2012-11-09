package com.townwizard.db.model;

import java.util.Date;

import javax.persistence.Column;
import javax.persistence.MappedSuperclass;

@MappedSuperclass
public abstract class AuditableEntity extends AbstractEntity {

    private static final long serialVersionUID = 1L;
    
    @Column(nullable = false, updatable = false) 
    private Date created;
    @Column(nullable = false)
    private Date updated;
    
    public Date getCreated() {
        return created;
    }
    public void setCreated(Date created) {
        this.created = created;
    }
    public Date getUpdated() {
        return updated;
    }
    public void setUpdated(Date updated) {
        this.updated = updated;
    }    
}