<?php

/**
 * This file is part of the GraphAware NeoClient package.
 *
 * (c) GraphAware <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\NeoClient\Formatter;

use GraphAware\NeoClient\Formatter\Graph\Graph;
use GraphAware\NeoClient\Formatter\Graph\NodesCollection;
use GraphAware\NeoClient\Formatter\Graph\Node;
use GraphAware\NeoClient\Formatter\Graph\Relationship;
use GraphAware\NeoClient\Formatter\Graph\RelationshipsCollection;

class ResultFormatter
{
    /**
     * @param array $result
     * @return \GraphAware\NeoClient\Formatter\Result
     */
    public function formatResult(array $result)
    {
        $columns = $result['columns'];
        $data = $result['data'][0];
        $graphData = isset($data['graph']) ? $data['graph'] : null;
        $restData = isset($data['rest']) ? $data['rest'] : null;
        $table = $this->processTableFormat($columns, $restData);
        $graph = $this->getGraphData($graphData);
        $identificationTable = $this->getIdentifiersReferences($table, $graph);

        return new Result($table, $graph, $identificationTable);
    }

    /**
     * @param array $columns
     * @param array $restData
     * @return \GraphAware\NeoClient\Formatter\Table
     */
    public function processTableFormat(array $columns, array $restData)
    {
        $table = new Table($columns);
        $cCount = count($columns);
        for ($i = 0; $i < $cCount; $i++) {
            $row = [];
            foreach ($columns as $x => $column) {
                $row[$column] = $this->getRestDataForTable($restData[$x]);
            }
            $table->addRow($row);
        }

        return $table;
    }

    /**
     * @param $data
     * @return array|int|string
     */
    public function getRestDataForTable($data)
    {
        if (is_int($data)) {
            return (int) $data;
        }

        if (is_string($data)) {
            return trim((string) $data);
        }

        if (is_array($data)) {
            if (!isset($data[0])) {
                return $this->formatColumnData($data);
            } else {
                $arr = [];
                foreach ($data as $child) {
                    $arr[] = $this->formatColumnData($child);
                }
                return $arr;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function formatColumnData(array $data)
    {
        if (isset($data['metadata'])) {
            if (isset($data['end']) && isset($data['start']) && isset($data['type'])) {
                $d = $data['metadata'];
                $d['data_type'] = 'relationship';
                $d['properties'] = $data['data'];
                return $d;
            }
            if (isset($data['labels']) && isset($data['incoming_relationships'])) {
                $d = $data['metadata'];
                $d['data_type'] = 'node';
                $d['properties'] = $data['data'];
                return $d;
            }
            return $data['metadata'];
        }
    }

    /**
     * @param array $graphData
     * @return \GraphAware\NeoClient\Formatter\Graph\Graph
     */
    public function getGraphData(array $graphData)
    {
        $nodes = new NodesCollection();
        $relationships = new RelationshipsCollection();
        foreach ($graphData['nodes'] as $data) {
            $node = new Node($data['id'], $data['labels'], $data['properties']);
            $nodes->addNode($node, $node->getId());
        }

        foreach ($graphData['relationships'] as $data) {
            $start = $nodes->getNode($data['startNode']);
            $end = $nodes->getNode($data['endNode']);
            $relationship = new Relationship($data['id'], $start, $end, $data['type'], $data['properties']);
            $relationships->addRelationship($relationship, $relationship->getId());
            $start->addRelationship($relationship);
            $end->addRelationship($relationship);
        }

        $graph = new Graph($nodes, $relationships);

        return $graph;
    }

    /**
     * @param \GraphAware\NeoClient\Formatter\Table $table
     * @param \GraphAware\NeoClient\Formatter\Graph\Graph $graph
     * @return array
     */
    public function getIdentifiersReferences(Table $table, Graph $graph)
    {
        $identificationTable = [];
        foreach ($table->getRows() as $row) {
            foreach ($row as $identifier => $value) {
                $identificationTable[$identifier][] = $value;
            }
        }

        foreach ($identificationTable as $identifier => $identifications) {
            foreach ($identifications as $i => $identification) {
                if (isset($identification['data_type'])) {
                    $identificationTable[$identifier][$i] = $this->transformIdentificationToObject($identification, $graph);
                } elseif (is_array($identification) && isset($identification[0])) {
                    foreach ($identification as $y => $subIdentification) {
                        if (isset($subIdentification['data_type'])) {
                            $identificationTable[$identifier][$i][$y] = $this->transformIdentificationToObject($subIdentification, $graph);
                        }
                    }
                }
            }
        }

        return $identificationTable;
    }

    /**
     * @param $identification
     * @param \GraphAware\NeoClient\Formatter\Graph\Graph $graph
     * @return \GraphAware\NeoClient\Formatter\Graph\Node|\GraphAware\NeoClient\Formatter\Graph\Relationship
     */
    protected function transformIdentificationToObject($identification, Graph $graph)
    {
        if ($identification['data_type'] === 'node') {
            return $graph->getNodesCollection()->getNode($identification['id']);
        } elseif ($identification['data_type'] === 'relationship') {
            return $graph->getRelationshipsCollection()->getRelationship($identification['id']);
        }

        return $identification;
    }
}