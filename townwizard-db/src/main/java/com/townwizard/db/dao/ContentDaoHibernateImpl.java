package com.townwizard.db.dao;

import org.springframework.stereotype.Component;

import com.townwizard.db.model.Content;
import com.townwizard.db.model.Content.ContentType;

@Component("contentDao")
public class ContentDaoHibernateImpl extends AbstractDaoHibernateImpl implements ContentDao {

    @Override
    public Content getContent(Integer siteId, ContentType contentType, Long externalContentId) {
        return (Content)getSession().createQuery(
                "from Content where " + 
                "externalId = :external_id and siteId = :site_id and contentType = :type and active = true")
                .setLong("external_id", externalContentId)
                .setInteger("site_id", siteId)
                .setInteger("type", contentType.getId()).uniqueResult();        
    }
    
}
