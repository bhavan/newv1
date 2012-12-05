package com.townwizard.db.services;

import com.townwizard.db.model.Content.ContentType;

public interface ContentService {

    Long saveUserRating(Long userId, Integer siteId, 
            ContentType contentType, Long externalContentId,  Float value);    
    
    Float getUserRating(Long userId, Integer siteId,
            ContentType contentType, Long externalContentId);
    
    Float[] getUserRatings(Long userId, Integer siteId,
            ContentType contentType, Long[] externalContentIds);    
    
    Float getAverageRating(Integer siteId,
            ContentType contentType, Long externalContentId);
}