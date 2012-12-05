package com.townwizard.db.model;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.EnumType;
import javax.persistence.Enumerated;

@Entity
public class Content extends AbstractEntity {     
        
    public static enum ContentType {
        ZERO(0, "Zero"), //to make sure Java Enum ordinals will start with 1 for hibernate mapping
        LOCATION(1, "Location"),
        EVENT(2, "Event");
        
        private final int id;
        private final String name;
        private ContentType(int id, String name) {
            this.id = id;
            this.name = name;
        }
        
        public int getId() {return id;}
        public String getName() {return name;}        
    }    
    
    private static final long serialVersionUID = -5577386542915432817L;

    private Long externalId;
    private Integer siteId;
    @Column(name="type_id")
    @Enumerated(EnumType.ORDINAL)
    private ContentType contentType;
    
    public Long getExternalId() {
        return externalId;
    }
    public void setExternalId(Long externalId) {
        this.externalId = externalId;
    }
    public Integer getSiteId() {
        return siteId;
    }
    public void setSiteId(Integer siteId) {
        this.siteId = siteId;
    }
    public ContentType getContentType() {
        return contentType;
    }
    public void setContentType(ContentType contentType) {
        this.contentType = contentType;
    }
    
}