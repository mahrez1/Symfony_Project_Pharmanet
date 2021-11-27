<?php

namespace App\Entity;
use App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /** 
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull
     * @Assert\Length(max=100,maxMessage="Votre commentaitre doit faire 100 caractéres au maximum")
     */
    private $content;

    /**
     * @ORM\Column(type="date")
     */
    private $creationdate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\OneToMany(targetEntity=LikeComment::class, mappedBy="comment", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Dislikecomment::class, mappedBy="comment", orphanRemoval=true)
     */
    private $dislike;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->dislike = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreationdate(): ?\DateTimeInterface
    {
        return $this->creationdate;
    }

    public function setCreationdate(\DateTimeInterface $creationdate): self
    {
        $this->creationdate = $creationdate;

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

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * @return Collection|LikeComment[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(LikeComment $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(LikeComment $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        } 

        return $this;
    }

    public function isLikedByUser(User $user) : bool
    {
     foreach ($this->likes as $like){
         if($like->getUser() === $user) return true ;
     }
    return false ;
    }

    /**
     * @return Collection|Dislikecomment[]
     */
    public function getDislike(): Collection
    {
        return $this->dislike;
    }

    public function addDislike(Dislikecomment $dislike): self
    {
        if (!$this->dislike->contains($dislike)) {
            $this->dislike[] = $dislike;
            $dislike->setComment($this);
        }

        return $this; 
    }

    public function removeDislike(Dislikecomment $dislike): self
    {
        if ($this->dislike->removeElement($dislike)) {
            // set the owning side to null (unless already changed)
            if ($dislike->getComment() === $this) {
                $dislike->setComment(null);
            }
        }

        return $this;
    }
    public function isdisLikedByUser(User $user) : bool
    {
     foreach ($this->dislike as $dislike){
         if($dislike->getUser() === $user) return true ;
     }
    return false ;
    }
}
