const date= new Date();
const error_json ={
	ingreso: null,
	salida: null,
	sueldo: null,
	nopagados: null,
	despidieron: null
}
const Formulario = {
	name: 'Formulario',
	template: `
    <h1>CALCULAR FINIQUITO</h1>
    <label>Fecha de ingreso</label>
    <div class="flex gap-1 my-1">
    	<select v-model="input.fecha.ingreso.dia" class="flex-auto">
    		<option disabled value="0">- DÍA -</option>
    		<option v-for="(d,k) in dia" :key="k" :value="d">{{d}}</option>
		</select>
    	<select v-model="input.fecha.ingreso.mes" class="w-6-12">
    		<option disabled value="0">- MES -</option>
    		<option v-for="(m,k) in mes" :key="k" :value="m">{{m}}</option>
		</select>
    	<select v-model="input.fecha.ingreso.ano" class="flex-auto">
    		<option disabled value="0">- AÑO -</option>
    		<option v-for="(m,k) in ano_in" :key="k" :value="m">{{m}}</option>
		</select>
	</div>
	<div class="error" v-if="error.ingreso">{{error.ingreso}}</div>
    
    <label>Fecha de salida</label>
    <div class="flex gap-1 my-1">
    	<select v-model="input.fecha.salida.dia" class="flex-auto">
    		<option disabled value="0">- DÍA -</option>
    		<option v-for="(d,k) in dia" :key="k" :value="d">{{d}}</option>
		</select>
    	<select v-model="input.fecha.salida.mes" class="w-6-12">
    		<option disabled value="0">- MES -</option>
    		<option v-for="(m,k) in mes" :key="k" :value="m">{{m}}</option>
		</select>
    	<select v-model="input.fecha.salida.ano" class="flex-auto">
    		<option disabled value="0">- AÑO -</option>
    		<option v-for="(m,k) in ano_sal" :key="k" :value="m">{{m}}</option>
		</select>
	</div>
	<div class="error" v-if="error.salida">{{error.salida}}</div>
	
	<label>Sueldo mensual</label>
	<div class="flex w-6-12 my-1">
		<button disabled>$</button><input v-model="input.sueldo" placeholder="MXN" type="number" class="flex-auto">
	</div>
	<div class="error" v-if="error.sueldo">{{error.sueldo}}</div>
	
	<label>Días trabajados no pagados</label>
	<div class="flex w-6-12 my-1">
		<input v-model="input.nopagados" type="number" class="flex-auto">
	</div>
	<div class="error" v-if="error.nopagados">{{error.nopagados}}</div>
	
	<label>¿Ya te despidieron?</label>
	<div class="flex my-1">
		<button class="w-6-12" :disabled="input.despidieron=='SI'" @click="input.despidieron='SI'">SI</button>
		<button class="w-6-12" :disabled="input.despidieron=='NO'" @click="input.despidieron='NO'">NO</button>
	</div>
	<div class="error" v-if="error.despidieron">{{error.despidieron}}</div>
	
	<div class="tc">
		<button class="submit" @click="enviar" class="w-6-12">CALCULAR</button>
	</div>
  `,
	data() {
		return {
			dia: [...Array(31)].map((_, i) => i+1),
			mes: [...Array(12)].map((_, i) => i+1),
			ano_in: [...Array(30)].map((_, i) => date.getFullYear()-i),
			ano_sal: [...Array(5)].map((_, i) => date.getFullYear()-i),
			input: {
				fecha:{
					ingreso:{
						dia: 0,
						mes: 0,
						ano: 0
					},
					salida:{
						dia: 0,
						mes: 0,
						ano: 0
					}
				},
				sueldo: null,
				nopagados: 0,
				despidieron: 'NO'
			},
			error: {...error_json},
		}
	},
	emits: ['input'],
	methods: {
		enviar(){
			this.error = {...error_json}
			const {ingreso, salida} = this.input.fecha
			const {sueldo, nopagados} = this.input
			if(!ingreso.dia || !ingreso.mes || !ingreso.ano){
				console.log('nanan')
				this.error.ingreso = 'La fecha es invalida!'
			}
			if(!salida.dia || !salida.mes || !salida.ano){
				this.error.salida = 'La fecha es invalida!'
			}
			if(ingreso.ano > salida.ano){
				this.error.fecha.salida = 'Es año es menor al ingreso!'
			}
			if(!sueldo || sueldo<0){
				this.error.sueldo = 'Sueldo invalido!'
			}
			if(typeof nopagados !='number' || nopagados<0){
				this.error.nopagados = 'No pagado tiene un valor invalido!'
			}

			const error = this.error
			if(!error.ingreso && !error.ingreso && !error.sueldo && !error.nopagados && !error.despidieron){
				this.$emit('input',this.input)
			}
		}
	}
}