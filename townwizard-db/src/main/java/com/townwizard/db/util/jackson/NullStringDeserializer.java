package com.townwizard.db.util.jackson;

import java.io.IOException;

import org.codehaus.jackson.JsonParser;
import org.codehaus.jackson.JsonProcessingException;
import org.codehaus.jackson.JsonToken;
import org.codehaus.jackson.map.DeserializationContext;
import org.codehaus.jackson.map.deser.std.StringDeserializer;

public class NullStringDeserializer extends StringDeserializer {

    @Override
    public String deserialize(JsonParser jp, DeserializationContext ctxt)
        throws IOException, JsonProcessingException {
        JsonToken curr = jp.getCurrentToken();
        if (curr == JsonToken.VALUE_STRING) {
            String text = jp.getText().trim();
            if(text.isEmpty()) return null;
            return text;
        }
        
        return super.deserialize(jp, ctxt);
    }
    
}