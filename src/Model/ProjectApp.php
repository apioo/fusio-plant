<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('An app of a project')]
class ProjectApp implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    #[Description('Name of your app i.e. backend or database must be unique within the project')]
    protected ?string $name = null;
    #[Description('Name of the docker image i.e. mysql:8.0')]
    protected ?string $image = null;
    #[Description('Optional a domain which is used for this app. If provided a sites-available entry will be registered and requests are routed to this app. Note make sure that the domain actual points to your server before using the domain so that it is possible to obtain an SSL certificate through certbot')]
    protected ?string $domain = null;
    #[Description('Optional if a domain was provided indicates whether nginx content caching is activated, this can heavily improve the performance of your service, should be used for every readonly app otherwise you need to think about cache invalidation')]
    protected ?bool $cache = null;
    #[Description('Optional if a domain was provided the internal port of the docker image which is exposed, if not provided port 80 is assumed')]
    protected ?int $port = null;
    /**
     * @var \PSX\Record\Record<string>|null
     */
    #[Description('Environment variables provided to the docker image')]
    protected ?\PSX\Record\Record $environment = null;
    /**
     * @var array<ProjectAppVolume>|null
     */
    #[Description('List of volumes which should be mounted to persist content')]
    protected ?array $volumes = null;
    /**
     * @var array<string>|null
     */
    #[Description('List of links to other apps')]
    protected ?array $links = null;
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }
    public function getDomain(): ?string
    {
        return $this->domain;
    }
    public function setCache(?bool $cache): void
    {
        $this->cache = $cache;
    }
    public function getCache(): ?bool
    {
        return $this->cache;
    }
    public function setPort(?int $port): void
    {
        $this->port = $port;
    }
    public function getPort(): ?int
    {
        return $this->port;
    }
    /**
     * @param \PSX\Record\Record<string>|null $environment
     */
    public function setEnvironment(?\PSX\Record\Record $environment): void
    {
        $this->environment = $environment;
    }
    /**
     * @return \PSX\Record\Record<string>|null
     */
    public function getEnvironment(): ?\PSX\Record\Record
    {
        return $this->environment;
    }
    /**
     * @param array<ProjectAppVolume>|null $volumes
     */
    public function setVolumes(?array $volumes): void
    {
        $this->volumes = $volumes;
    }
    /**
     * @return array<ProjectAppVolume>|null
     */
    public function getVolumes(): ?array
    {
        return $this->volumes;
    }
    /**
     * @param array<string>|null $links
     */
    public function setLinks(?array $links): void
    {
        $this->links = $links;
    }
    /**
     * @return array<string>|null
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('name', $this->name);
        $record->put('image', $this->image);
        $record->put('domain', $this->domain);
        $record->put('cache', $this->cache);
        $record->put('port', $this->port);
        $record->put('environment', $this->environment);
        $record->put('volumes', $this->volumes);
        $record->put('links', $this->links);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

