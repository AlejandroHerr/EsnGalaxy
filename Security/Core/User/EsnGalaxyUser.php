<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\User;

use Symfony\Component\Security\Core\User\UserInterface;

class EsnGalaxyUser extends ModelBase implements UserInterface
{
    /**
     * @var string Username
     */
    protected $username;
    /**
     * @var string E-mail
     */
    protected $mail;
    /**
     * @var string Section Code
     */
    protected $sc;
    /**
     * @var string|null First Name
     */
    protected $first;
    /**
     * @var string|null Last Name
     */
    protected $last;
    /**
     * @var array Roles
     */
    protected $roles = array();
    /**
     * @var string Link to the picture
     */
    protected $picture;
    /**
     * @var string|null Birthdate
     */
    protected $birthdate;
    /**
     * @var string|null Gender
     */
    protected $gender;
    /**
     * @var int|null Telephone
     */
    protected $telephone;
    /**
     * @var string|null Adress
     */
    protected $adress;
    /**
     * @var string Section Name
     */
    protected $section;
    /**
     * @var string Country
     */
    protected $country;

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }
    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username();
    }
    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        return null;
    }
    /**
     * Sets the value of username.
     *
     * @param string Username $username the username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
    /**
     * Gets the value of mail.
     *
     * @return string E-mail
     */
    public function getMail()
    {
        return $this->mail;
    }
    /**
     * Sets the value of mail.
     *
     * @param string E-mail $mail the mail
     *
     * @return self
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }
    /**
     * Gets the value of sc.
     *
     * @return string Section Code
     */
    public function getSc()
    {
        return $this->sc;
    }
    /**
     * Sets the value of sc.
     *
     * @param string Section Code $sc the sc
     *
     * @return self
     */
    public function setSc($sc)
    {
        $this->sc = $sc;

        return $this;
    }
    /**
     * Gets the value of first.
     *
     * @return string|null First Name
     */
    public function getFirst()
    {
        return $this->first;
    }
    /**
     * Sets the value of first.
     *
     * @param string|null First Name $first the first
     *
     * @return self
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }
    /**
     * Gets the value of last.
     *
     * @return string|null Last Name
     */
    public function getLast()
    {
        return $this->last;
    }
    /**
     * Sets the value of last.
     *
     * @param string|null Last Name $last the last
     *
     * @return self
     */
    public function setLast($last)
    {
        $this->last = $last;

        return $this;
    }
    /**
     * Sets the value of roles.
     *
     * @param array|string Roles $roles the roles
     *
     * @return self
     */
    public function setRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = explode(',', $roles);
        }
        $this->roles = $roles;

        return $this;
    }
    /**
     * Gets the value of picture.
     *
     * @return string Link to the picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
    /**
     * Sets the value of picture.
     *
     * @param string Link to the picture $picture the picture
     *
     * @return self
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }
    /**
     * Gets the value of birthdate.
     *
     * @return string|null Birthdate
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }
    /**
     * Sets the value of birthdate.
     *
     * @param string|null Birthdate $birthdate the birthdate
     *
     * @return self
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }
    /**
     * Gets the value of gender.
     *
     * @return string|null Gender
     */
    public function getGender()
    {
        return $this->gender;
    }
    /**
     * Sets the value of gender.
     *
     * @param string|null Gender $gender the gender
     *
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }
    /**
     * Gets the value of telephone.
     *
     * @return int|null Telephone
     */
    public function getTelephone()
    {
        return $this->telephone;
    }
    /**
     * Sets the value of telephone.
     *
     * @param int|null Telephone $telephone the telephone
     *
     * @return self
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }
    /**
     * Gets the value of adress.
     *
     * @return string|null Adress
     */
    public function getAdress()
    {
        return $this->adress;
    }
    /**
     * Sets the value of adress.
     *
     * @param string|null Adress $adress the adress
     *
     * @return self
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }
    /**
     * Gets the value of section.
     *
     * @return string Section Name
     */
    public function getSection()
    {
        return $this->section;
    }
    /**
     * Sets the value of section.
     *
     * @param string Section Name $section the section
     *
     * @return self
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }
    /**
     * Gets the value of country.
     *
     * @return string Country
     */
    public function getCountry()
    {
        return $this->country;
    }
    /**
     * Sets the value of country.
     *
     * @param string Country $country the country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }
}
