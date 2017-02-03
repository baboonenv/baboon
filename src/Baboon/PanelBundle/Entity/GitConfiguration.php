<?php

namespace Baboon\PanelBundle\Entity;

/**
 * Class GitConfiguration
 * @package Baboon\PanelBundle\Entity
 */
class GitConfiguration
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $deployType = 'ssh';

    /**
     * @var string|null
     */
    private $repo;

    /**
     * @var string
     */
    private $branch = 'master';

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDeployType(): string
    {
        return $this->deployType;
    }

    /**
     * @param string $deployType
     *
     * @return $this
     */
    public function setDeployType(string $deployType)
    {
        $this->deployType = $deployType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @param null|string $repo
     *
     * @return $this
     */
    public function setRepo($repo)
    {
        $this->repo = $repo;

        return $this;
    }

    /**
     * @return string
     */
    public function getBranch(): string
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     *
     * @return $this
     */
    public function setBranch(string $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}