<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @property  conference
 * @property Conference|null conference
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Conference", inversedBy="votes")
     */
    private $conference;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userVote")
     */
    private $user;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getScore(): ?int
    {
        return $this->score;
    }
    public function setScore(?int $score): self
    {
        $this->score = $score;
        return $this;
    }
    public function getConference(): ?Conference
    {
        return $this->conference;
    }
    public function setConference(?Conference $conference): self
    {
        $this->conference = $conference;
        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}