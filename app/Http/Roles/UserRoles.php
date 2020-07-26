<?php

namespace App\Roles;

class UserRoles
{
  const ROLE_ADMIN = 'admin';
  const ROLE_CONTRIBUTOR = 'contributor';
  const ROLE_USER = 'user';

  /**
   * @var array
   */
  protected static $roleHierarchy = [
      self::ROLE_ADMIN,
      self::ROLE_CONTRIBUTOR,
      self::ROLE_USER,
  ];

  /**
   * Checks user's role against heirarchy to see what roles user can use
   * @param string $userRole
   * @return array
   */
  public static function getAllowedRoles(string $userRole)
  {
      $index = array_search($userRole,self::$roleHierarchy);
      if (false !== $index && isset(self::$roleHierarchy[$index])) {
          return array_slice(self::$roleHierarchy,$index);
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
