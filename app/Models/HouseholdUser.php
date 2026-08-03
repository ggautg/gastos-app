<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdUser extends Model
{

    protected $fillable = ['household_id', 'user_id','role','created_at', 'updated_at'];

    protected $table = 'household_user';
}

/*
CREATE TABLE `household_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `household_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` enum('owner','viewer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `household_user_household_id_user_id_unique` (`household_id`,`user_id`),
  KEY `household_user_user_id_foreign` (`user_id`),
  CONSTRAINT `household_user_household_id_foreign` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE,
  CONSTRAINT `household_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/