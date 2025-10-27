import React from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import PrimaryButton from "@/Components/PrimaryButton";
import { router } from "@inertiajs/react";

export default function Dashboard({ auth, projects }) {
    const { post, processing } = useForm({});

  const generateReport = () => {
    post(route("reports.generate"), {
        preserveScroll: true,
        onSuccess: () => {
            alert("Report Generated, Report generation job dispatched!");
            router.visit(route("reports.index"));
        },
        onError: () => alert("Failed to dispatch report job."),
    });
};
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium text-gray-900">
                                    Projects Overview
                                </h3>
                                <div className="flex gap-2">
                                    <Link
                                        href={route("projects.create")}
                                        className="inline-flex items-center rounded-md border border-transparent bg-green-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-green-600 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                                    >
                                        Create Project
                                    </Link>
                                    <PrimaryButton
                                        onClick={generateReport}
                                        disabled={processing}
                                    >
                                        {processing
                                            ? "Generating..."
                                            : "Generate Reports Now"}
                                    </PrimaryButton>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {projects.map((project) => (
                                    <div
                                        key={project.id}
                                        className="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200"
                                    >
                                        <h4 className="text-xl font-semibold text-gray-800 mb-2">
                                            <a
                                                href={route(
                                                    "projects.show",
                                                    project.id
                                                )}
                                                className="hover:underline text-blue-600"
                                            >
                                                {project.name}
                                            </a>
                                        </h4>
                                        <p className="text-gray-600 text-sm mb-4 line-clamp-2">
                                            {project.description}
                                        </p>
                                        <div className="space-y-1 text-sm">
                                            <p>
                                                <span className="font-medium">
                                                    Total Tasks:
                                                </span>{" "}
                                                {project.total_tasks}
                                            </p>
                                            <p>
                                                <span className="font-medium text-blue-600">
                                                    Pending:
                                                </span>{" "}
                                                {project.pending_tasks}
                                            </p>
                                            <p>
                                                <span className="font-medium text-yellow-600">
                                                    In Progress:
                                                </span>{" "}
                                                {project.in_progress_tasks}
                                            </p>
                                            <p>
                                                <span className="font-medium text-green-600">
                                                    Completed:
                                                </span>{" "}
                                                {project.completed_tasks}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            {projects.length === 0 && (
                                <p className="text-gray-500 text-center mt-8">
                                    No projects found. Create one!
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
