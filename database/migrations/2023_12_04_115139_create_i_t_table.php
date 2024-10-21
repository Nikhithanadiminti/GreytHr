<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('i_t', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('it_emp_id', 10)->nullable()->default(null)->unique();
            $table->string('employee_name');
            $table->string('emp_id', 10)->nullable();
            $table->string('delete_itmember_reason', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('role')->default(0); // Role column: 0 = user, 1 = admin, 2 = super admin
            $table->string('password', 100)->nullable();
            $table->timestamps();

            $table->foreign('emp_id')
                ->references('emp_id')
                ->on('employee_details')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Creating a trigger to auto-generate it_emp_id in the format IT-10000, IT-10001, etc.
        $triggerSQL = <<<SQL
        CREATE TRIGGER generate_it_emp_id BEFORE INSERT ON i_t FOR EACH ROW
        BEGIN
            IF NEW.it_emp_id IS NULL THEN
                -- Fetch the maximum numeric value from it_emp_id
                SET @max_id := IFNULL(
                    (SELECT MAX(CAST(SUBSTRING(it_emp_id, 4) AS UNSIGNED)) FROM i_t),
                    10000
                );

                -- Increment and assign the new it_emp_id
                SET NEW.it_emp_id = CONCAT('IT-', @max_id + 1);
            END IF;
        END;
        SQL;

        DB::unprepared($triggerSQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the trigger first before dropping the table
        DB::unprepared('DROP TRIGGER IF EXISTS generate_it_emp_id');
        Schema::dropIfExists('i_t');
    }
};
