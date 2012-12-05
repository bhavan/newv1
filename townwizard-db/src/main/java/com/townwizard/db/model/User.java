package com.townwizard.db.model;

import java.util.Map;

import javax.persistence.CascadeType;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.EnumType;
import javax.persistence.Enumerated;
import javax.persistence.OneToOne;

import org.codehaus.jackson.annotate.JsonIgnore;

import com.townwizard.db.util.EmailValidator;

@Entity
public class User extends AuditableEntity {
    
    public static enum LoginType {
        ZERO(0, "Zero"), //to make sure Java Enum ordinals will start with 1 for hibernate mapping
        TOWNWIZARD(1, "Townwizard"),
        FACEBOOK(2, "Facebook"),
        TWITTER(2, "Twitter");
        
        private final int id;
        private final String name;
        private LoginType(int id, String name) {
            this.id = id;
            this.name = name;
        }
        
        public int getId() {return id;}
        public String getName() {return name;}
        
        public static LoginType byId(int id) {
            switch(id) {
            case 1: return TOWNWIZARD;
            case 2: return FACEBOOK;
            case 3: return TWITTER;
            default: return ZERO;
            }            
        }
    }     

    private static final long serialVersionUID = -6562731576094594464L;
    
    private String username;
    private String email;
    private String password;
    private String firstName;
    private String lastName;
    private Integer year;
    private String gender;
    private String mobilePhone;
    private String registrationIp;
    @Column(name="login_type_id")
    @Enumerated(EnumType.ORDINAL)
    private LoginType loginType;
    private Long externalId;
    
    @OneToOne(mappedBy = "user", cascade = {CascadeType.ALL})
    private Address address;
    
    public String getUsername() {
        return username;
    }
    public void setUsername(String username) {
        this.username = username;
    }
    public String getEmail() {
        return email;
    }
    public void setEmail(String email) {
        this.email = email;
    }
    public String getPassword() {
        return password;
    }
    public void setPassword(String password) {
        this.password = password;
    }
    public String getFirstName() {
        return firstName;
    }
    public void setFirstName(String firstName) {
        this.firstName = firstName;
    }
    public String getLastName() {
        return lastName;
    }
    public void setLastName(String lastName) {
        this.lastName = lastName;
    }
    public Address getAddress() {
        return address;
    }
    public void setAddress(Address address) {
        this.address = address;
        if(address != null) {
            address.setUser(this);
        }
    }
    public Integer getYear() {
        return year;
    }
    public void setYear(Integer year) {
        this.year = year;
    }
    public String getGender() {
        return gender;
    }
    public void setGender(String gender) {
        this.gender = gender;
    }
    public String getMobilePhone() {
        return mobilePhone;
    }
    public void setMobilePhone(String mobilePhone) {
        this.mobilePhone = mobilePhone;
    }
    public String getRegistrationIp() {
        return registrationIp;
    }
    public void setRegistrationIp(String registrationIp) {
        this.registrationIp = registrationIp;
    }    
    public LoginType getLoginType() {
        return loginType;
    }
    public void setLoginType(LoginType loginType) {
        this.loginType = loginType;
    }
    public Long getExternalId() {
        return externalId;
    }
    public void setExternalId(Long externalId) {
        this.externalId = externalId;
    }
    @JsonIgnore
    public boolean isValid() {
        if(getLoginType() == null) {
            return false;
        }        
        if(getLoginType().equals(LoginType.TOWNWIZARD)) {
            return isEmailValid() && isPasswordValid();
        }
        return true;
    }
    
    public User asJsonView() {
        setPassword(null);
        return this;
    }
    
    public static User fromFbUser(Map<String, Object> fbUser) throws NumberFormatException {
        //fbUser represents the following JSON
        
        //{"id":"1205619426","name":"Vladimir Mazheru","first_name":"Vladimir","last_name":"Mazheru",
        //"link":"http:\/\/www.facebook.com\/vmazheru","username":"vmazheru","gender":"male",
        //"email":"v_mazheru\u0040yahoo.com","timezone":-5,"locale":"en_US","verified":true,
        //"updated_time":"2012-11-27T20:05:07+0000"}
        
        User u = new User();
        u.setExternalId(new Long((String)fbUser.get("id")));
        u.setEmail((String)fbUser.get("email"));
        u.setFirstName((String)fbUser.get("first_name"));
        u.setLastName((String)fbUser.get("last_name"));
        u.setUsername((String)fbUser.get("username"));
        String gender = (String)fbUser.get("gender");
        if(gender != null && !gender.isEmpty()) {
            switch(gender.charAt(0)) {
            case 'm': u.setGender("M"); break;
            case 'f': u.setGender("F"); break;
            }
        }
        u.setLoginType(LoginType.FACEBOOK);
        
        return u;
    }

    @Override
    public String toString() {
        return "User [username=" + username + ", email=" + email
                + ", firstName=" + firstName
                + ", lastName=" + lastName + ", year=" + year + ", gender="
                + gender + ", mobilePhone=" + mobilePhone + ", registrationIp="
                + registrationIp + ", address=" + address + "]";
    }
    
    private boolean isEmailValid() {        
        return EmailValidator.isValidEmailAddress(getEmail());
    }
    
    private boolean isPasswordValid() {
        return (password != null);
    }
}