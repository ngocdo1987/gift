<?php
// This file was auto-generated from sdk-root/src/data/dynamodb/2012-08-10/waiters-2.json
return [ 'version' => 2, 'waiters' => [ 'TableExists' => [ 'acceptors' => [ [ 'argument' => 'Table.TableStatus', 'expected' => 'ACTIVE', 'matcher' => 'path', 'state' => 'success', ], [ 'expected' => 'ResourceNotFoundException', 'matcher' => 'error', 'state' => 'retry', ], ], 'delay' => 20, 'maxAttempts' => 25, 'operation' => 'DescribeTable', ], 'TableNotExists' => [ 'acceptors' => [ [ 'expected' => 'ResourceNotFoundException', 'matcher' => 'error', 'state' => 'success', ], ], 'delay' => 20, 'maxAttempts' => 25, 'operation' => 'DescribeTable', ], ],];
