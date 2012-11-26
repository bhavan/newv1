package com.townwizard.db.dao;

import com.townwizard.db.model.Content;
import com.townwizard.db.model.Content.ContentType;

public interface ContentDao extends AbstractDao {
    Content getContent(Integer siteId, ContentType contentType, Long externalContentId);
}