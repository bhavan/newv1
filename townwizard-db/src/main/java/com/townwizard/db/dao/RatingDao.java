package com.townwizard.db.dao;

import com.townwizard.db.model.Content;
import com.townwizard.db.model.Rating;
import com.townwizard.db.model.User;

public interface RatingDao extends AbstractDao { 
    Rating getRating(User user, Content content);
    Float getAverageRating(Content content);
}
