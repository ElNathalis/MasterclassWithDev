<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 255)->after('email');            // обязательное
            $table->string('phone', 20)->unique()->nullable(false)->after('full_name'); // уникальное, обязательное
            $table->enum('role', ['visitor', 'pending', 'master', 'admin'])
                ->default('visitor')
                ->after('phone');
            $table->string('photo')->nullable()->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'phone', 'role', 'photo']);
        });
    }
};
