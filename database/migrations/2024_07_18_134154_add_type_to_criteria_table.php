<?php

// database/migrations/xxxx_xx_xx_add_type_to_criteria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToCriteriaTable extends Migration
{
    public function up()
    {
        Schema::table('criteria', function (Blueprint $table) {
            $table->string('type')->default('benefit'); // Add a new column for type
        });
    }

    public function down()
    {
        Schema::table('criteria', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
