package com.townwizard.db.services;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

import com.townwizard.db.dao.ContentDao;
import com.townwizard.db.dao.RatingDao;
import com.townwizard.db.model.Content;
import com.townwizard.db.model.Content.ContentType;
import com.townwizard.db.model.Rating;
import com.townwizard.db.model.User;

@Component("contentService")
@Transactional
public class ContentServiceImpl implements ContentService {
    
    @Autowired
    private ContentDao contentDao;
    @Autowired
    private RatingDao ratingDao;

    @Override
    public Long saveUserRating(Long userId, Integer siteId,
            ContentType contentType, Long externalContentId, Float value) {
        Content c = contentDao.getContent(siteId, contentType, externalContentId);
        if(c == null) {
            c = createContent(siteId, contentType, externalContentId); 
            contentDao.create(c);
        }
        User u = new User();
        u.setId(userId);
        Rating r = ratingDao.getRating(u, c);
        if(r == null) {
            r = createRating(u, c, value);
            ratingDao.create(r);
        } else {
            r.setValue(new Float(value));
            ratingDao.update(r);
        }
        return r == null ? null : r.getId();
    }
    
    @Override
    public Float getUserRating(Long userId, Integer siteId,
            ContentType contentType, Long externalContentId) {
        Float rating = null;
        Content c = contentDao.getContent(siteId, contentType, externalContentId);
        if(c != null) {
            User u = new User();
            u.setId(userId);
            Rating r = ratingDao.getRating(u, c);
            if(r != null) {
                rating = r.getValue();
            }
        }
        return rating;
    }
    
    @Override
    public Float[] getUserRatings(Long userId, Integer siteId,
            ContentType contentType, Long[] externalContentIds) {
        Float[] retVal = new Float[externalContentIds.length];
        for(int i = 0; i < externalContentIds.length; i++) {
            retVal[i] = getUserRating(userId, siteId, contentType, externalContentIds[i]);
        }
        return retVal;
    } 

    @Override
    public Float getAverageRating(Integer siteId, ContentType contentType,
            Long externalContentId) {
        Float rating = 0F;
        Content c = contentDao.getContent(siteId, contentType, externalContentId);
        if(c != null) {
            rating = ratingDao.getAverageRating(c);            
        }
        return rating;
    }
    
    private Content createContent(Integer siteId, ContentType contentType, Long externalContentId) {
        Content c = new Content();
        c.setSiteId(siteId);
        c.setContentType(contentType);
        c.setExternalId(externalContentId);
        return c;
    }
    
    private Rating createRating(User user, Content content, Float value) {
        Rating r = new Rating();
        r.setUser(user);
        r.setContent(content);
        r.setValue(value);
        return r;
    }
    
}
