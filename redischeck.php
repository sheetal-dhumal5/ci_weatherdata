<?php
try {
    // Create a Redis Instance
    $redis = new \Redis();
    
    // Try to connect to a redis server
    // In this case within the host machine and the default port of redis
    $redis->connect('127.0.0.1', 6379);

    // Define some Key
    $redis->set('user', 'redisuser');

    // Obtain value
    $user = $redis->get('user');

    // Should Output: sdkcarlos
    print($user);
} catch (Exception $ex) {
    echo $ex->getMessage();
}
?>