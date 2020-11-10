# Simple Yii2 OAuth2 Server

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run:

```shell script
php composer.phar require --prefer-dist evgenidev/yii2-simple-oauth2server "*"
```

or add:

```json
"evgenidev/yii2-simple-oauth2server": "*"
```

to the `require` section of your composer.json.


To use this extension, add the following code in your application configuration:

```php
'modules' => [
    'oauth2' => [
        'class' => \EvgeniDev\Yii2\OAuth2Server\Module::class,
        'accessTokenLifetime' => 3600 * 12,
        'identityClass' => \app\models\User::class,
    ],
],
```

Also add bootstrap param:

```php
'bootstrap' => [
    'oauth2',
],
```

If you want to add a custom authorize view file simple add a authorizeView parameter to oauth2 module.

```php
'modules' => [
    'oauth2' => [
        'class' => \EvgeniDev\Yii2\OAuth2Server\Module::class,
        'accessTokenLifetime' => 3600 * 12,
        'identityClass' => \app\models\User::class,
        'authorizeViewPath' => '@app/views/your_view',
        'layout' => '@app/views/your_layout',
    ],
],
```

The basic authorize view you can find here:
 
`./vendor/yii2-simple-oauth2server/src/views/authorize/index.php`

The next step you should run migration.

```shell script
./yii migrate --migrationPath=@vendor/evgenidev/yii2-simple-oauth2server/src/migrations
```

This migration creates the oauth2 database scheme and insert test data.

On the next step you should add url rule to urlManager, like this:

```php
'urlManager' => [
    'rules' => [
        'oauth2/authorize' => 'oauth2/authorize',
        'oauth2/access_token' => 'oauth2/access-token',
    ],
],
```

## Usage

It is simple to add a new OAuth client. Use a command:

```shell script
./yii oauth2/default/create-client http://redirect.com clientName
```

GET request example to get a code:

`https://yoursite.com/oauth/authorize?response_type=code&client_id=clientID&state=someState&redirect_uri=http://site.com/url`

With redirect response:

`http://site.com/url?code=gjkmo5ufhvkdmjgnbdJklsdfFQPfdfg456nfdsjfnjsdnf&state=someState`

After that you need to do a POST request with params like:

`https://yoursite.com/oauth/access_token`

```json
"grant_type": "authorization_code"
"code": "gjkmo5ufhvkdmjgnbdJklsdfFQPfdfg456nfdsjfnjsdnf"
"client_id": "testClientID"
"client_secret": "testClientSecret"
"redirect_uri": "http://site.com/url"
```
If user is unauthorized, module will redirect to Yii::$app->user->loginUrl with GET param redirectUrl:

`https://yoursite.com/loginUrl?redirectUrl=xxx`

So you can redirect user to a redirectUrl after success authorization.

If you want to control OAuth2 server through the interface (admin and etc.), you can find all necessary models to do that in:

`./vendor/yii2-simple-oauth2server/src/records/*`

and

`./vendor/yii2-simple-oauth2server/src/services/*`

To use this extension, simply add the behaviors for your base controller:

```php
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class Controller extends \yii\rest\Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    ['class' => QueryParamAuth::class, 'tokenParam' => 'accessToken'],
                ],
            ],
        ]);
    }
}
```

To identify a client you can use a function findIdentityByAccessToken() in your User identity AR model:

```php
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthAccessToken;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User AR model.
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritDoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $oauthToken = OAuthAccessToken::find()
            ->byAccessToken($token)
            ->one();
    
        if ($oauthToken === null || $oauthToken->getExpiresAt() < date('Y-m-d H:i:s')) {
            return null;
        }
    
        return self::find()
            ->where(['id' => $oauthToken->getUserID()])
            ->one();
    }
}
```

