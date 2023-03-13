const Calculo = {
	name: 'Calculo',
	template: `
	<!--	<pre>{{total}}</pre>-->
    <h1>Finiquito</h1>
    <div class="flex">
    	<div class="w-6-12 my-1">
    		Fecha ingreso
    		<br>
    		<b>{{total.inicio}}</b>
		</div>
    	<div class="w-6-12 my-1">
    		Fecha salida
    		<br>
    		<b>{{total.fin}}</b>
		</div>
	</div>
	
    <div class="flex">
    	<div class="w-6-12 my-1">
    		Tiempo trabajando
    		<br>
    		<b>{{total.dias}} días</b>
		</div>
    	<div class="w-6-12 my-1">
    		Vacaciones
    		<br>
    		<b>{{total.vacaciones_dias}} dias</b>
		</div>
	</div>
	
	<table>
		<tr><td>Salario mensual</td><td>$ {{total.salario.toFixed(2)}}</td></tr>
		<tr><td>Salario devengado</td><td>$ {{total.denegado.toFixed(2)}}</td></tr>
		<tr><td>Aguinaldo</td><td>$ {{total.aguinaldo.toFixed(2)}}</td></tr>
		<tr><td>Vacaciones</td><td>$ {{total.vacaciones.toFixed(2)}}</td></tr>
		<tr><td>Prima vacacional</td><td>$ {{total.prima.toFixed(2)}}</td></tr>
		<tr><td>TOTAL</td><td><h4>$ {{total.total.toFixed(2)}}</h4></td></tr>
	</table>
	
	<h2>¿Cuánto cobrarías en 12 meses que dura el juicio si metieras la demanda hoy?</h2>
	<table>
		<tr><td>Tope Salarios Caídos</td><td>$ {{(total.salario*12).toFixed(2)}}</td></tr>
		<tr><td>Subtotal</td><td>144,000.00</td></tr>
		<tr><td>Intereses Mensuales</td><td>0.00</td></tr>
		<tr><td>TOTAL</td><td><h4>201,901.03</h4></td></tr>
	</table> 
	
	<div class="tc">
		<button class="submit" @click="whats_send" class="w-6-12">¡Quiero demandar!</button>
	</div>

`,
	props:['input', 'whats'],
	computed:{
		total(){
			const {ingreso, salida} = this.input.fecha
			const {sueldo, nopagados} = this.input

			const MESES = [
				"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
				"Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
			];

			const fecha_inicio = new Date(ingreso.ano, ingreso.mes-1, ingreso.dia)
			const inicio = `${ingreso.dia}/${MESES[ingreso.mes-1]}/${ingreso.ano}`
			const fecha_fin = new Date(salida.ano, salida.mes-1, salida.dia)
			const fin = `${salida.dia}/${MESES[salida.mes-1]}/${salida.ano}`

			const diffTime = Math.abs(fecha_inicio - fecha_fin)
			const dias = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

			const vacaciones_list = {
				1 : 12,
				2 : 14,
				3 : 16,
				4 : 18,
				5 : 20
			};

			[...Array(5)].forEach((_, i) => {
				vacaciones_list[i+6] = 22
				vacaciones_list[i+11] = 24
				vacaciones_list[i+16] = 26
				vacaciones_list[i+21] = 28
				vacaciones_list[i+26] = 30
				vacaciones_list[i+31] = 32
			})

			const vacaciones_dias = vacaciones_list[Math.floor(dias/365)]

			const salario = sueldo
			const denegado = salario * nopagados

			const sal_base = salario/30
			const diasAno = Math.abs(fecha_fin - new Date())
			const E4 = Math.ceil(diasAno / (1000 * 60 * 60 * 24))
			const E5 = E4/365
			const aguinaldo = sal_base*15*E5

			const vacaciones = sal_base*14*E5

			const prima = 0.25*vacaciones

			const p_antiguedad = prima*0.25

			const sal_integr = (salario+500)/70

			const dias45 = sal_integr*45

			const total = aguinaldo+vacaciones+prima+p_antiguedad+dias45

			return {
				inicio,
				fin,
				dias,
				vacaciones_dias,
				salario,
				denegado,
				aguinaldo,
				vacaciones,
				prima,
				total,
			}
		}
	},
	methods:{
		whats_send(){
			const li = this.total
			const data = `Fecha ingreso: ${li.inicio}
					Fecha salida: ${li.fin}
					Tiempo trabajando: ${li.dias}
					Vacaciones: ${li.vacaciones_dias}
					Salario mensual: $ ${li.salario.toFixed(2)}
					Salario devengado: $ ${li.denegado.toFixed(2)}
					Aguinaldo: $ ${li.aguinaldo.toFixed(2)}
					Vacaciones: $ ${li.vacaciones.toFixed(2)}
					Prima vacacional: $ ${li.prima.toFixed(2)}
					TOTAL: $ ${li.total.toFixed(2)}
					
					. ${this.input.despidieron} ME DESPIDIERON!
					`

			const txt = `Quiero demandar! ${data}`

			window.open(`https://wa.me/${this.whats}?text=${encodeURI(txt)}`, '_blank');
			console.log('whats')
		}
	}
}