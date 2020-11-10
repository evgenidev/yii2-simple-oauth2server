<?php

declare(strict_types = 1);

use EvgeniDev\Yii2\OAuth2Server\Components\Migration;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient;

/**
 * OAuth2 Server schema.
 */
final class m201106_191741_oauth2server extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function safeUp()
    {
        // OAuthClient table.
        $this->createTable('OAuthClient', [
            'clientID' => $this->string(48)->notNull(),
            'name' => $this->string(255)->notNull(),
            'clientSecret' => $this->string(48)->defaultValue(null),
            'redirectUri' => 'varchar(1000) NOT NULL',
            'grantTypes' => $this->string(100)->notNull(),
            'userID' => $this->string()->defaultValue(null),
        ]);

        $this->createSimpleIndex('OAuthClient', ['name']);
        $this->createSimpleIndex('OAuthClient', ['clientID'], true);
        $this->createSimpleIndex('OAuthClient', ['clientSecret'], true);
        $this->createSimpleIndex('OAuthClient', ['name', 'redirectUri'], true);
        $this->createSimpleIndex('OAuthClient', ['userID']);
        $this->addPrimaryKey('OAuthClientPK', 'OAuthClient', 'clientID');

        // OAuthAccessToken table.
        $this->createTable('OAuthAccessToken', [
            'accessToken' => $this->string(48)->notNull(),
            'clientID' => $this->string(48)->notNull(),
            'userID' => $this->string()->notNull(),
            'expiresAt' => $this->timestamp()->notNull(),
        ]);

        $this->createSimpleIndex('OAuthAccessToken', ['accessToken'], true);
        $this->createSimpleIndex('OAuthAccessToken', ['clientID']);
        $this->createSimpleIndex('OAuthAccessToken', ['userID']);

        $this->addSimpleForeignKey('OAuthAccessToken', ['clientID'], 'OAuthClient', ['clientID']);
        $this->addPrimaryKey('OAuthAccessTokenPK', 'OAuthAccessToken', 'accessToken');

        // OAuthAuthorizationCode table.
        $this->createTable('OAuthAuthorizationCode', [
            'authorizationCode' => $this->string(48)->notNull(),
            'clientID' => $this->string(48)->notNull(),
            'userID' => $this->string()->notNull(),
            'redirectUri' => 'varchar(1000) NOT NULL',
        ]);

        $this->createSimpleIndex('OAuthAuthorizationCode', ['authorizationCode'], true);
        $this->createSimpleIndex('OAuthAuthorizationCode', ['clientID']);
        $this->createSimpleIndex('OAuthAuthorizationCode', ['userID']);
        $this->createSimpleIndex('OAuthAuthorizationCode', ['redirectUri']);

        $this->addSimpleForeignKey('OAuthAuthorizationCode', ['clientID'], 'OAuthClient', ['clientID']);
        $this->addPrimaryKey('OAuthAuthorizationCodePK', 'OAuthAuthorizationCode', 'authorizationCode');

        // OAuthApproval table.
        $this->createTable('OAuthApproval', [
            'ID' => $this->primaryKey(12),
            'userID' => $this->string()->notNull(),
            'clientID' => $this->string(48)->notNull(),
        ]);

        $this->createSimpleIndex('OAuthApproval', ['userID', 'clientID'], true);
        $this->addSimpleForeignKey('OAuthApproval', ['clientID'], 'OAuthClient', ['clientID']);
        $this->createSimpleIndex('OAuthApproval', ['ID'], true);

        if (YII_ENV_DEV) {
            $this->insert('OAuthClient', [
                'name' => 'Test client',
                'clientID' => 'testClientID',
                'clientSecret' => 'testClientSecret',
                'redirectUri' => 'http://site.com/url',
                'grantTypes' => OAuthClient::GRANT_TYPE_CODE,
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function safeDown()
    {
        $this->dropTable('OAuthApproval');
        $this->dropTable('OAuthAuthorizationCode');
        $this->dropTable('OAuthAccessToken');
        $this->dropTable('OAuthClient');
    }
}
