<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(PaisesTableSeeder::class);
    	$this->call(DepartamentosTableSeeder::class);
    	$this->call(MunicipioTableSeeder::class);
    	$this->call(RolesTableSeeder::class);
        $this->call(EmpresasTableSeeder::class);
        $this->call(AfpsTableSeeder::class);
        $this->call(ArlsTableSeeder::class);
        $this->call(EspecialidadTableSeeder::class);
    	$this->call(UsersTableSeeder::class);
        $this->call(ModulosTableSeeder::class);
        $this->call(TipoFactorRiesgosSeeder::class);
        $this->call(ActividadesTableSeeder::class);
        $this->call(FactorRiesgosSeeder::class);
        $this->call(EnfermedadTableSeeder::class);
        $this->call(EscolaridadTableSeeder::class);
        $this->call(LateralidadesTableSeeder::class);
        $this->call(LesionesTableSeeder::class);
        $this->call(TurnosTableSeeder::class);
        $this->call(TipoExamenesTableSeeder::class);
        $this->call(OjosTableSeeder::class);
        $this->call(TipoExamenVisualesTableSeeder::class);
        $this->call(CantidadFumadoresTableSeeder::class);
        $this->call(TiempoFumadoresTableSeeder::class);
        $this->call(TiempoLicoresTableSeeder::class);
        $this->call(RegularidadMedicamentosTableSeeder::class);
        $this->call(ExamenVisualesTableSeeder::class);
        $this->call(TipoOrganosTableSeeder::class);
        $this->call(OrganosTableSeeder::class);
        $this->call(TipoExamenAlturasSeeder::class);
        $this->call(TipoConsentimientosTableSeeder::class);
        $this->call(TipoCondicionesTableSeeder::class);
        $this->call(GinecologiaExploracionPeriodosTableSeeder::class);
    }
}
