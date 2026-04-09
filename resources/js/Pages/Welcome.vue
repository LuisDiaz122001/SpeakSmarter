<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
    overview: {
        type: Object,
        required: true,
    },
    featuredLessons: {
        type: Array,
        required: true,
    },
    featuredCategories: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const priceFormatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'USD',
});

const benefitCards = [
    {
        title: 'Explora antes de entrar',
        description: 'El cliente puede recorrer la oferta, comparar niveles y entender cada leccion antes de iniciar sesion.',
    },
    {
        title: 'Compra por leccion',
        description: 'Cada leccion puede tener su precio propio, sin obligar a presentar un plan unico o una oferta rigida.',
    },
    {
        title: 'Catalogo con estructura',
        description: 'Categorias, niveles y contenido destacado ayudan a que la experiencia se sienta ordenada y mas confiable.',
    },
];

const learningSteps = [
    {
        title: 'Descubre el catalogo',
        description: 'Revisa la portada, identifica las categorias y ubica las lecciones que mejor se ajustan al objetivo del alumno.',
    },
    {
        title: 'Compara nivel y precio',
        description: 'Cada tarjeta resume el nivel, el valor y el enfoque de la leccion para tomar una decision rapida.',
    },
    {
        title: 'Entra y continua',
        description: 'Cuando el usuario ya entiende la propuesta, pasa al flujo autenticado con mucha menos friccion.',
    },
];

const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const authPrimaryHref = computed(() => {
    if (isAuthenticated.value) {
        return route('dashboard');
    }

    return props.canLogin ? route('login') : '#lessons';
});
const authPrimaryLabel = computed(() => (isAuthenticated.value ? 'Ir al dashboard' : 'Iniciar sesion'));
const heroLessons = computed(() => props.featuredLessons.slice(0, 2));
const hasLessons = computed(() => props.featuredLessons.length > 0);
const stats = computed(() => [
    {
        title: 'Lecciones publicadas',
        value: props.overview.lessons,
        note: 'Contenido visible para el cliente desde la portada.',
    },
    {
        title: 'Categorias activas',
        value: props.overview.categories,
        note: 'Rutas tematicas para navegar la biblioteca.',
    },
    {
        title: 'Lecciones gratis',
        value: props.overview.free_lessons,
        note: 'Contenido de entrada para activar confianza rapido.',
    },
    {
        title: 'Lecciones pagas',
        value: props.overview.paid_lessons,
        note: 'Oferta premium lista para mostrar precio por pieza.',
    },
]);

const startingPrice = computed(() => {
    if (props.overview.starting_price === null) {
        return 'Catalogo flexible';
    }

    return `Desde ${priceFormatter.format(Number(props.overview.starting_price))}`;
});

const displayPrice = (lesson) => {
    if (lesson.is_free) {
        return 'Gratis';
    }

    if (lesson.price === null) {
        return 'Consultar precio';
    }

    return priceFormatter.format(Number(lesson.price));
};

const priceCaption = (lesson) => {
    if (lesson.is_free) {
        return 'Acceso sin costo para captar interes';
    }

    if (lesson.price === null) {
        return 'Configura el valor desde el panel administrativo';
    }

    return 'Pago individual por leccion';
};
</script>

<template>
    <Head title="SpeakSmarter" />

    <div class="workspace-shell relative min-h-screen overflow-hidden text-slate-900">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-[-12%] top-[-6rem] h-[26rem] w-[26rem] rounded-full bg-orange-300/20 blur-3xl" />
            <div class="absolute right-[-10%] top-[8rem] h-[24rem] w-[24rem] rounded-full bg-teal-300/18 blur-3xl" />
            <div class="absolute inset-x-0 top-[26rem] h-px bg-gradient-to-r from-transparent via-slate-300/50 to-transparent" />
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 py-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white shadow-[0_18px_40px_rgba(15,23,42,0.16)]">
                        SS
                    </div>
                    <div>
                        <p class="workspace-display text-xl text-slate-900">SpeakSmarter</p>
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Lesson-first learning</p>
                    </div>
                </div>

                <div class="hidden items-center gap-6 lg:flex">
                    <a href="#lessons" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Lecciones</a>
                    <a href="#how-it-works" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Como funciona</a>
                    <a href="#pricing" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Precios</a>
                </div>

                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                    <Link
                        v-if="canLogin"
                        :href="authPrimaryHref"
                        class="inline-flex w-full justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto"
                    >
                        {{ authPrimaryLabel }}
                    </Link>
                    <Link
                        v-if="!isAuthenticated && canRegister"
                        :href="route('register')"
                        class="inline-flex w-full justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 sm:w-auto"
                    >
                        Crear cuenta
                    </Link>
                </div>
            </header>

            <main class="flex-1 py-4 sm:py-8">
                <section class="workspace-hero rounded-[1.5rem] px-4 py-6 text-white sm:rounded-[2rem] sm:px-8 sm:py-8 lg:px-10">
                    <div
                        class="absolute inset-0"
                        style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(15, 118, 110, 0.9));"
                    />

                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1.45fr_0.9fr]">
                        <div class="space-y-6">
                            <div class="space-y-4">
                                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Landing comercial</p>
                                <h1 class="workspace-display max-w-3xl text-3xl leading-tight sm:text-5xl lg:text-6xl">
                                    Muestra cada leccion con su precio y convierte la portada en una experiencia de venta mas clara.
                                </h1>
                                <p class="max-w-2xl text-sm leading-7 text-white/78 sm:text-base">
                                    SpeakSmarter ahora puede funcionar como una vitrina publica: el cliente explora, compara, entiende el valor y entra a la plataforma con mejor contexto.
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <a
                                    href="#lessons"
                                    class="inline-flex w-full justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100 sm:w-auto"
                                >
                                    Ver lecciones destacadas
                                </a>
                                <a
                                    href="#pricing"
                                    class="inline-flex w-full justify-center rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20 sm:w-auto"
                                >
                                    Explorar precios
                                </a>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="rounded-[1.35rem] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur">
                                    <p class="text-xs uppercase tracking-[0.22em] text-white/60">Catalogo</p>
                                    <p class="mt-3 text-2xl font-semibold text-white">{{ overview.lessons }}</p>
                                    <p class="mt-2 text-sm text-white/74">lecciones visibles</p>
                                </div>
                                <div class="rounded-[1.35rem] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur">
                                    <p class="text-xs uppercase tracking-[0.22em] text-white/60">Categorias</p>
                                    <p class="mt-3 text-2xl font-semibold text-white">{{ overview.categories }}</p>
                                    <p class="mt-2 text-sm text-white/74">rutas de aprendizaje</p>
                                </div>
                                <div class="rounded-[1.35rem] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur">
                                    <p class="text-xs uppercase tracking-[0.22em] text-white/60">Precio</p>
                                    <p class="mt-3 text-2xl font-semibold text-white">{{ startingPrice }}</p>
                                    <p class="mt-2 text-sm text-white/74">referencia inicial</p>
                                </div>
                            </div>
                        </div>

                        <div class="workspace-panel rounded-[1.75rem] p-5 text-slate-900">
                            <p class="workspace-kicker">Preview comercial</p>
                            <h2 class="workspace-display mt-3 text-2xl leading-tight text-slate-900">
                                Una portada lista para recorrer la oferta sin sentir ruido visual.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                El cliente ve precio, nivel y contexto desde la primera visita. El equipo mantiene el control del contenido desde el panel.
                            </p>

                            <div class="mt-6 space-y-3">
                                <article
                                    v-for="lesson in heroLessons"
                                    :key="lesson.id"
                                    class="rounded-[1.25rem] border border-slate-200 bg-white px-4 py-4"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ lesson.name }}</p>
                                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ displayPrice(lesson) }}</p>
                                        </div>
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                                            {{ lesson.level || 'Open level' }}
                                        </span>
                                    </div>
                                </article>
                            </div>

                            <div v-if="!heroLessons.length" class="mt-6 rounded-[1.25rem] border border-dashed border-slate-300 bg-white px-4 py-6 text-sm leading-7 text-slate-500">
                                Agrega lecciones con precio desde el panel y apareceran aqui automaticamente.
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="card in stats"
                        :key="card.title"
                        class="workspace-stat-card rounded-[1.5rem] p-5"
                    >
                        <p class="workspace-kicker">{{ card.title }}</p>
                        <p class="workspace-display mt-4 text-3xl text-slate-900 sm:text-4xl">{{ card.value }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ card.note }}</p>
                    </article>
                </section>

                <section class="mt-6 grid gap-6 lg:grid-cols-3">
                    <article
                        v-for="benefit in benefitCards"
                        :key="benefit.title"
                        class="workspace-panel rounded-[1.75rem] p-6"
                    >
                        <p class="workspace-kicker">Valor visible</p>
                        <h2 class="workspace-display mt-3 text-2xl leading-tight text-slate-900">{{ benefit.title }}</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ benefit.description }}</p>
                    </article>
                </section>

                <section id="lessons" class="mt-6 workspace-panel rounded-[1.9rem] p-6 sm:p-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="workspace-kicker">Lecciones destacadas</p>
                            <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900 sm:text-4xl">
                                Un recorrido tipo catalogo para que cada visita entienda que se ofrece y cuanto cuesta.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                Esta seccion usa las lecciones reales del sistema. Si una leccion es gratis se muestra como entrada sin costo. Si es paga, aparece con su precio.
                            </p>
                        </div>

                        <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            {{ hasLessons ? `${featuredLessons.length} visibles` : 'Sin lecciones publicadas' }}
                        </span>
                    </div>

                    <div v-if="hasLessons" class="mt-8 grid gap-4 xl:grid-cols-2">
                        <article
                            v-for="lesson in featuredLessons"
                            :key="lesson.id"
                            class="workspace-list-card rounded-[1.6rem] p-5 sm:p-6"
                        >
                            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">
                                            {{ lesson.level || 'Nivel flexible' }}
                                        </span>
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]"
                                            :class="lesson.is_free ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-900 text-white'"
                                        >
                                            {{ displayPrice(lesson) }}
                                        </span>
                                    </div>

                                    <h3 class="workspace-display mt-4 text-2xl leading-tight text-slate-900">{{ lesson.name }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ lesson.description }}</p>

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <span
                                            v-for="category in lesson.categories"
                                            :key="`${lesson.id}-${category}`"
                                            class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                                        >
                                            {{ category }}
                                        </span>
                                        <span
                                            v-if="!lesson.categories.length"
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500"
                                        >
                                            Sin categoria todavia
                                        </span>
                                    </div>
                                </div>

                                <div class="w-full min-w-0 rounded-[1.35rem] bg-slate-50 px-4 py-4 lg:min-w-[12rem] lg:w-auto">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Precio publico</p>
                                    <p class="mt-3 workspace-display text-3xl text-slate-900">{{ displayPrice(lesson) }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ priceCaption(lesson) }}</p>
                                    <p class="mt-4 text-xs uppercase tracking-[0.2em] text-slate-400">{{ lesson.updated_at }}</p>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-else class="workspace-empty mt-8 rounded-[1.6rem] px-6 py-10 text-center">
                        <h3 class="workspace-display text-2xl text-slate-900">Aun no hay lecciones visibles</h3>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                            La estructura comercial ya esta lista. Solo falta publicar lecciones con precio desde el panel para llenar este catalogo automaticamente.
                        </p>
                    </div>
                </section>

                <section id="pricing" class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_1fr]">
                    <article class="workspace-panel rounded-[1.9rem] p-6 sm:p-8">
                        <p class="workspace-kicker">Precios y decision</p>
                        <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900 sm:text-4xl">
                            Una forma mas simple de vender: mostrar la leccion, su enfoque y su valor sin esconder la oferta.
                        </h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            En vez de una portada generica, ahora el cliente puede entender el catalogo como una biblioteca premium: algunas lecciones atraen con acceso gratis y otras convierten con precio individual.
                        </p>

                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-[1.3rem] bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Precio inicial</p>
                                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ startingPrice }}</p>
                            </div>
                            <div class="rounded-[1.3rem] bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Gratis</p>
                                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ overview.free_lessons }}</p>
                            </div>
                            <div class="rounded-[1.3rem] bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Pagas</p>
                                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ overview.paid_lessons }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="workspace-panel rounded-[1.9rem] p-6 sm:p-8">
                        <p class="workspace-kicker">Mapa del contenido</p>
                        <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900">Categorias para explorar la oferta sin perderse</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            El cliente tambien puede hacerse una idea rapida de la biblioteca completa viendo las categorias mas activas.
                        </p>

                        <div v-if="featuredCategories.length" class="mt-6 flex flex-wrap gap-3">
                            <div
                                v-for="category in featuredCategories"
                                :key="category.id"
                                class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700"
                            >
                                {{ category.name }} - {{ category.lessons_count }} lecciones
                            </div>
                        </div>

                        <div v-else class="workspace-empty mt-6 rounded-[1.5rem] px-5 py-8 text-center">
                            <p class="text-sm leading-7 text-slate-600">
                                Cuando empieces a organizar el contenido por categorias, esta zona se convertira en una guia de exploracion para el cliente.
                            </p>
                        </div>
                    </article>
                </section>

                <section id="how-it-works" class="mt-6 workspace-panel rounded-[1.9rem] p-6 sm:p-8">
                    <div class="max-w-3xl">
                        <p class="workspace-kicker">Como funciona</p>
                        <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900 sm:text-4xl">
                            Una portada pensada para descubrir, comparar y luego entrar al producto.
                        </h2>
                    </div>

                    <div class="mt-8 grid gap-4 lg:grid-cols-3">
                        <article
                            v-for="(step, index) in learningSteps"
                            :key="step.title"
                            class="workspace-list-card rounded-[1.5rem] p-5"
                        >
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                                {{ index + 1 }}
                            </span>
                            <h3 class="workspace-display mt-4 text-2xl text-slate-900">{{ step.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ step.description }}</p>
                        </article>
                    </div>
                </section>

                <section class="mt-6 workspace-hero rounded-[1.5rem] px-4 py-6 text-white sm:rounded-[2rem] sm:px-8 sm:py-8">
                    <div
                        class="absolute inset-0"
                        style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(194, 65, 12, 0.9));"
                    />

                    <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Siguiente paso</p>
                            <h2 class="workspace-display mt-3 text-3xl leading-tight sm:text-5xl">
                                La portada ya puede vender mejor. El siguiente movimiento es poblarla con lecciones reales y precios claros.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                Desde aqui puedes seguir afinando la experiencia publica o conectar un flujo de compra y detalle de leccion mas adelante.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a
                                href="#lessons"
                                class="inline-flex w-full justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100 sm:w-auto"
                            >
                                Revisar catalogo
                            </a>
                            <Link
                                v-if="canLogin"
                                :href="authPrimaryHref"
                                class="inline-flex w-full justify-center rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20 sm:w-auto"
                            >
                                {{ authPrimaryLabel }}
                            </Link>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>
