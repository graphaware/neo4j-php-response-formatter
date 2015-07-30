<?php

/**
 * This file is part of the GraphAware NeoClient package.
 *
 * (c) GraphAware <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\NeoClient\Formatter\Graph;

class Node
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $labels;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @var \GraphAware\NeoClient\Formatter\Graph\RelationshipsCollection
     */
    protected $relationships;

    /**
     * @param int $id
     * @param array $labels
     * @param array $properties
     * @param \GraphAware\NeoClient\Formatter\Graph\Relationship[] $relationships
     */
    public function __construct($id, array $labels, array $properties, RelationshipsCollection $relationshipsCollection = null)
    {
        $this->id = (int) $id;
        $this->labels = $labels;
        $this->properties = $properties;
        $this->relationships = $relationshipsCollection !== null ? $relationshipsCollection : new RelationshipsCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getLabels() {
        return $this->labels;
    }

    /**
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * @return \GraphAware\NeoClient\Formatter\Graph\Relationship[]
     */
    public function getRelationships() {
        return $this->relationships;
    }

    public function addRelationship(Relationship $relationship)
    {
        $this->relationships->addRelationship($relationship);
    }


}