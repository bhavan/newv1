package com.townwizard.db.model.dto;

import org.codehaus.jackson.annotate.JsonIgnore;

import com.townwizard.db.model.Content.ContentType;

public class RatingDTO {
    
    private Long userId;
    private Integer siteId;
    private Long contentId;
    private Float value;
    private ContentType contentType;
    
    public RatingDTO() {}
    
    public RatingDTO(Long userId, Integer siteId, Long contentId, Float value, ContentType contentType) {
        this.userId = userId;
        this.siteId = siteId;
        this.contentId = contentId;
        this.value = value;
        this.contentType = contentType;
    }
    
    public Long getUserId() {
        return userId;
    }
    public void setUserId(Long userId) {
        this.userId = userId;
    }
    public Integer getSiteId() {
        return siteId;
    }
    public void setSiteId(Integer siteId) {
        this.siteId = siteId;
    }
    public Long getContentId() {
        return contentId;
    }
    public void setContentId(Long contentId) {
        this.contentId = contentId;
    }
    public Float getValue() {
        return value;
    }
    public void setValue(Float value) {
        this.value = value;
    }
    public ContentType getContentType() {
        return contentType;
    }
    public void setContentType(ContentType contentType) {
        this.contentType = contentType;
    }
    
    @JsonIgnore
    public boolean isValid() {
        return userId != null && siteId != null && contentId != null && value != null && contentType != null;
    }

}
