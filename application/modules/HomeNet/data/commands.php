<?php
/* 
 * commands.php
 * 
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * 
 * This file is part of HomeNet.
 * 
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */
$c = array();
$c[0x00] = array('name' => 'ERROR',          'type' => HomeNet_Model_Payload::STRING,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Error message');
$c[0x01] = array('name' => 'VERSION',        'type' => HomeNet_Model_Payload::FLOAT,     'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get current version of firmware on the node');
$c[0x03] = array('name' => 'BATTERY LEVEL',  'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get current battery level');
$c[0x04] = array('name' => 'FREE MEMORY',    'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get free amount of memory');
$c[0x33] = array('name' => 'PING',           'type' => HomeNet_Model_Payload::STRING,    'reply' => HomeNet_Model_Packet::PONG,  'description' => 'Send Test Ping, node should reply back pong');
$c[0x3e] = array('name' => 'PONG',           'type' => HomeNet_Model_Payload::STRING,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Response to a Ping Command');
$c[0x11] = array('name' => 'ACK',            'type' => HomeNet_Model_Payload::BYTE,      'reply' => null,                        'description' => 'Acknowledge Packet, letting the sender of a packet know that data arrived safely');
$c[0x21] = array('name' => 'GET NODE ID',    'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get Node\'s current ID (Used with node 0 broadcast)');
$c[0x22] = array('name' => 'SET NODE ID',    'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Changes Node ID, used for initial setup');
$c[0x23] = array('name' => 'GET DEVICE',     'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get device id code');
$c[0x24] = array('name' => 'SET DEVICE',     'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set device code (future use)');

$c[0xB1] = array('name' => 'AUTO SEND START','type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get device id code');
$c[0xB2] = array('name' => 'AUTO SEND STOP', 'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set device code (future use)');

$c[0xC0] = array('name' => 'ON/OPEN',        'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Turn Device on');
$c[0xC1] = array('name' => 'OFF/CLOSE',      'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Turn Device off');
$c[0xC2] = array('name' => 'LEVEL',          'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set device light level');
$c[0xC3] = array('name' => 'CLEAR',          'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Clear device');

$c[0xD0] = array('name' => 'GET VALUE',      'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value in native format from component byte');
$c[0xD1] = array('name' => 'GET BYTE',       'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');
$c[0xD2] = array('name' => 'GET STRING',     'type' => HomeNet_Model_Payload::STRING,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');
$c[0xD3] = array('name' => 'GET INT',        'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');
$c[0xD4] = array('name' => 'GET FLOAT',      'type' => HomeNet_Model_Payload::FLOAT,     'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');
$c[0xD5] = array('name' => 'GET LONG',       'type' => HomeNet_Model_Payload::LONG,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');
$c[0xD6] = array('name' => 'GET BINARY',     'type' => HomeNet_Model_Payload::BINARY,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');
$c[0xD7] = array('name' => 'GET BOOLEAN',    'type' => HomeNet_Model_Payload::BOOLEAN,   'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Get value');

$c[0xE0] = array('name' => 'SET VALUE',      'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set raw value');
$c[0xE1] = array('name' => 'SET BYTE',       'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');
$c[0xE2] = array('name' => 'SET STRING',     'type' => HomeNet_Model_Payload::STRING,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');
$c[0xE3] = array('name' => 'SET INT',        'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');
$c[0xE4] = array('name' => 'SET FLOAT',      'type' => HomeNet_Model_Payload::FLOAT,     'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');
$c[0xE5] = array('name' => 'SET LONG',       'type' => HomeNet_Model_Payload::LONG,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');
$c[0xE6] = array('name' => 'SET BINARY',     'type' => HomeNet_Model_Payload::BINARY,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');
$c[0xE7] = array('name' => 'SET BOOLEAN',    'type' => HomeNet_Model_Payload::BOOLEAN,   'reply' => HomeNet_Model_Packet::ACK,   'description' => 'Set value');

$c[0xF0] = array('name' => 'REPLY VALUE',    'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF1] = array('name' => 'REPLY BYTE',     'type' => HomeNet_Model_Payload::BYTE,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF2] = array('name' => 'REPLY STRING',   'type' => HomeNet_Model_Payload::STRING,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF3] = array('name' => 'REPLY INT',      'type' => HomeNet_Model_Payload::INT,       'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF4] = array('name' => 'REPLY FLOAT',    'type' => HomeNet_Model_Payload::FLOAT,     'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF5] = array('name' => 'REPLY LONG',     'type' => HomeNet_Model_Payload::LONG,      'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF6] = array('name' => 'REPLY BINARY',   'type' => HomeNet_Model_Payload::BINARY,    'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');
$c[0xF7] = array('name' => 'REPLY BOOLEAN',  'type' => HomeNet_Model_Payload::BOOLEAN,   'reply' => HomeNet_Model_Packet::ACK,   'description' => 'A reply value');

//01	VERSION		FLOAT	ACK	Get current version of firmware on the node
//03	BATTERY LEVEL	BLANK	ACK	Get current battery level
//04	FREE MEMORY	BLANK	ACK	Get current battery level
//33	PING 		STRING	PONG 	ACK	Send Test Ping, node should reply back pong
//3E	PONG 		STRING	ACK		Response to a Ping Command
//11	ACK 		BYTE 	BYTE 	Acknowledge Packet, letting the sender of a packet know that data arrived safely
//21	GET NOD EID	INT	ACK 	packetid 	BYTE 	Acknowledge Packet, letting the sender of a packet know that data arrived safely
//21	SET NODE ID	INT 	ACK	Get Node's current ID (Used with node 0 broadcast)
//23	GET DEVICE 	BYTE	ACK 	Get the device code for what is attached on the node
//24	SET DEVICE 	BYTE 	ACK 	Changes Device ID, used for initial setup
//
//B1	AUTO SEND START	INT	ACK 	Set up the node to automatically
//B2	AUTO SEND STOP 	INT 	ACK 	Stop auto sending sensor data
//
//C0	ON 		BYTE	ACK 	Simple Turn on
//C1	OFF 		BYTE	ACK 	Simple Turn Off
//C2	LEVEL 		BYTE	ACK 	Set a light to a level 0-255
//C3	CLEAR 		BYTE	ACK 	Clear LCD

//E0	SET VALUE 	INT 	ACK
//E1	SET BYTE 	BYTE	ACK
//E2	SET STRING 	STRING	ACK
//E3	SET INT 	INT	ACK
//E4	SET FLOAT 	FLOAT	ACK
//E5	SET LONG 	LONG	ACK
//E6	SET BINARY	BINARY	ACK
//F0	REPLY VALUE 	BYTE	ACK
//F1	REPLY BYTE 	BYTE	ACK
//F2	REPLY STRING 	STRING	ACK
//F3	REPLY INT 	INT	ACK
//F4	REPLY FLOAT 	FLOAT	ACK
//F5	REPLY LONG 	LONG	ACK
//F6	REPLY BINARY	BINARY	ACK
//FF	REPLY ERROR	BINARY	ACK

return $c;