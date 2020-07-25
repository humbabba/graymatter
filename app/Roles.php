<?php

namespace App;

class Role
{
  const ROLE_ADMIN = 'admin';
  const ROLE_CONTRIBUTOR = 'contributor';
  const ROLE_USER = 'user';

  /**
   * @var array
   */
  protected static $roleHierarchy = [
      self::ROLE_ADMIN => [
        self::ROLE_ADMIN,
        self::ROLE_CONTRIBUTOR,
        self::ROLE_USER,
      ],
      self::ROLE_CONTRIBUTOR => [
          self::ROLE_CONTRIBUTOR,
          self::ROLE_USER,
      ],
      self::ROLE_USER => [
        self::ROLE_USER,
      ]
  ];

  /**
   * @param string $role
   * @return array
   */
  public static function getAllowedRoles(string $role)
  {
      if (isset(self::$roleHierarchy[$role])) {
          return self::$roleHierarchy[$role];
      }

      return [];
  }

  /***
   * @return array
   */
  public static function getRoleList()
  {
      return [
          static::ROLE_ADMIN => 'Admin',
          static::ROLE_CONTRIBUTOR => 'Contributor',
          static::ROLE_USER => 'User',
      ];
  }
}
