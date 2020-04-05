<?php

declare(strict_types=1);

use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * ユーザー情報クラス
 */
class PublicKeyCredentialUserEntityRepository
{

    /**
     * usernameでユーザー情報を取得
     * @param string $username
     * @return PublicKeyCredentialUserEntity|null
     */
    public function findWebauthnUserByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        $WebauthnCredential = DB::queryFirstRow("SELECT * FROM webauthn_credentials WHERE user_handle = %s", $username);

        if (!$WebauthnCredential) {
            return null;
        }
        return new PublicKeyCredentialUserEntity(
            $WebauthnCredential['user_handle'],
            $WebauthnCredential['user_handle'],
            strtoupper($WebauthnCredential['user_handle'])
        );
    }

    /**
     * userhandleでユーザー情報を取得
     * @param string $userHandle
     * @return PublicKeyCredentialUserEntity|null
     */
    public function findWebauthnUserByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        $WebauthnCredential = DB::queryFirstRow("SELECT * FROM webauthn_credentials WHERE user_handle = %s", $userHandle);

        if (!$WebauthnCredential) {
            return null;
        }
        return new PublicKeyCredentialUserEntity(
            $WebauthnCredential['user_handle'],
            $WebauthnCredential['user_handle'],
            strtoupper($WebauthnCredential['user_handle'])
        );

    }
}