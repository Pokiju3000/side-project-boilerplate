import Alpine from 'alpinejs';
import React from 'react';
import { createRoot } from 'react-dom/client';

window.Alpine = Alpine;

Alpine.start();

function DiagnosticApp({ payload }) {
    const checks = payload.checks || [];
    const summary = payload.summary || [];
    const labels = payload.labels || {};
    const groups = [...new Set(checks.map((check) => check.group))];

    return (
        <div className="space-y-6">
            <div className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p className="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">{payload.eyebrow}</p>
                    <h1 className="mt-2 text-3xl font-semibold text-[#16324f]">{payload.title}</h1>
                    <p className="mt-3 max-w-3xl text-sm leading-6 text-[#5f7083]">{payload.intro}</p>
                </div>
                <button
                    type="button"
                    onClick={() => window.location.reload()}
                    className="inline-flex items-center justify-center rounded-lg bg-[#2f58e8] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#284dca]"
                >
                    {labels.refresh}
                </button>
            </div>

            <section className="grid gap-4 md:grid-cols-3">
                {summary.map((item) => (
                    <article key={item.label} className="rounded-lg border border-[#d8e4f1] bg-white p-5 shadow-sm">
                        <p className="text-xs font-semibold uppercase tracking-[0.16em] text-[#6b7c90]">{item.label}</p>
                        <p className="mt-3 text-3xl font-semibold text-[#16324f]">{item.value}</p>
                        <p className="mt-2 text-sm leading-6 text-[#5f7083]">{item.detail}</p>
                    </article>
                ))}
            </section>

            {groups.map((group) => (
                <section key={group} className="space-y-3">
                    <h2 className="text-sm font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{group}</h2>
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {checks.filter((check) => check.group === group).map((check) => (
                            <CheckCard key={check.key} check={check} labels={labels} />
                        ))}
                    </div>
                </section>
            ))}
        </div>
    );
}

function CheckCard({ check, labels }) {
    const styles = {
        up: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        warning: 'border-amber-200 bg-amber-50 text-amber-700',
        down: 'border-rose-200 bg-rose-50 text-rose-700',
    };

    return (
        <article className="rounded-lg border border-[#d8e4f1] bg-white p-5 shadow-sm">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-[#6b7c90]">{check.area}</p>
                    <h3 className="mt-3 text-lg font-semibold text-[#16324f]">{check.label}</h3>
                </div>
                <span className={`rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] ${styles[check.status] || styles.down}`}>
                    {labels.status?.[check.status] || check.status}
                </span>
            </div>
            <p className="mt-4 break-words text-sm leading-6 text-[#5f7083]">{check.detail}</p>
            {check.hint ? <p className="mt-3 rounded-lg bg-[#eef4ff] px-3 py-2 text-xs font-medium leading-5 text-[#2f58e8]">{check.hint}</p> : null}
        </article>
    );
}

const diagnosticRoot = document.getElementById('diagnostic-root');

if (diagnosticRoot) {
    createRoot(diagnosticRoot).render(<DiagnosticApp payload={JSON.parse(diagnosticRoot.dataset.payload || '{}')} />);
}
