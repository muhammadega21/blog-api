<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create([
            'role_name' => 'Administrator'
        ]);
        Role::create([
            'role_name' => 'Author'
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345'),
            'user_profile' => 'img/user.png',
            'user_desc' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. A asperiores optio dolorum sapiente quia ea accusantium quisquam dolorem provident molestiae repellat eligendi atque architecto, eos at ducimus nemo eum vitae aut rem saepe itaque molestias! Inventore blanditiis autem rem ipsa suscipit reprehenderit incidunt sint iste, repellat in, praesentium omnis eveniet.',
            'role_id' => 1
        ]);

        User::create([
            'name' => 'Muhammad Ega Dermawan',
            'email' => 'dermawane988@gmail.com',
            'password' => Hash::make('12345'),
            'user_profile' => 'img/user.png',
            'user_desc' => '<h2>Hi! i’m Saimon D’silva</h2>
                            <p>Dynamically underwhelm integrated outsourcing via timely models. Rapidiously reconceptualize visionary imperatives without 24/365 catalysts for change. Completely streamline functionalized models and out-of-the-box functionalities. Authoritatively target proactive vortals vis-a-vis exceptional results. Compellingly brand emerging sources and compelling materials. Globally iterate parallel content</p>
                            <h5>The best ideas can change who we are.</h5>
                            <p>Dynamically underwhelm integrated outsourcing via timely models. Rapidiously reconceptualize visionary imperatives without 24/365 catalysts for</p>
                            ',
            'ig_url' => 'https://www.instagram.com/kzm.mv/',
            'fb_url' => 'https://www.facebook.com/ega.dermawan.280899',
            'role_id' => 2
        ]);

        Category::create([
            'category_name' => 'Technology',
            'category_slug' => 'technology',
            'category_icon' => 'icon/icon.png',
        ]);
        Category::create([
            'category_name' => 'Holiday & Travel',
            'category_slug' => 'holiday-&-travel',
            'category_icon' => 'icon/icon.png',
        ]);
        Category::create([
            'category_name' => 'Food',
            'category_slug' => 'food',
            'category_icon' => 'icon/icon.png',
        ]);

        Post::create([
            'post_title' => 'test 1',
            'post_slug' => 'test-1',
            'post_content' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. A asperiores optio dolorum sapiente quia ea accusantium quisquam dolorem provident molestiae repellat eligendi atque architecto, eos at ducimus nemo eum vitae aut rem saepe itaque molestias! Inventore blanditiis autem rem ipsa suscipit reprehenderit incidunt sint iste, repellat in, praesentium omnis eveniet.',
            'post_image' => 'img/post.png',
            'publish_date' => date('Y-m-d'),
            'user_id' => 2,
            'category_id' => 1
        ]);
        Post::create([
            'post_title' => 'test 2',
            'post_slug' => 'test-2',
            'post_content' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. A asperiores optio dolorum sapiente quia ea accusantium quisquam dolorem provident molestiae repellat eligendi atque architecto, eos at ducimus nemo eum vitae aut rem saepe itaque molestias! Inventore blanditiis autem rem ipsa suscipit reprehenderit incidunt sint iste, repellat in, praesentium omnis eveniet.',
            'post_image' => 'img/post.png',
            'publish_date' => date('Y-m-d'),
            'user_id' => 2,
            'category_id' => 2
        ]);
    }
}
