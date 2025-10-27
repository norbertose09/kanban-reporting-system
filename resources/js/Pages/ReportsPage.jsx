import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function ReportsPage({ auth, projects }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Project Reports
                </h2>
            }
        >
            <Head title="Reports" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-xl font-medium text-gray-900 mb-6">
                                Latest Reports for Projects
                            </h3>

                            {projects.length === 0 && (
                                <p className="text-gray-500 text-center">
                                    No projects to display reports for.
                                </p>
                            )}

                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {projects.map((project) => (
                                    <div
                                        key={project.id}
                                        className="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200"
                                    >
                                        <h4 className="text-xl font-semibold text-gray-800 mb-3">
                                            {project.name}
                                        </h4>
                                        {project.latest_report ? (
                                            <div className="space-y-2 text-sm">
                                                <p>
                                                    <span className="font-medium">
                                                        Total Tasks:
                                                    </span>{" "}
                                                    {
                                                        project.latest_report
                                                            .total_tasks
                                                    }
                                                </p>
                                                <p>
                                                    <span className="font-medium text-green-600">
                                                        Completed Tasks:
                                                    </span>{" "}
                                                    {
                                                        project.latest_report
                                                            .completed_tasks
                                                    }
                                                </p>
                                                <p>
                                                    <span className="font-medium text-blue-600">
                                                        Pending Tasks:
                                                    </span>{" "}
                                                    {
                                                        project.latest_report
                                                            .pending_tasks
                                                    }
                                                </p>
                                                <p>
                                                    <span className="font-medium text-yellow-600">
                                                        In Progress Tasks:
                                                    </span>{" "}
                                                    {
                                                        project.latest_report
                                                            .in_progress_tasks
                                                    }
                                                </p>
                                                <p className="text-xs text-gray-500 mt-3">
                                                    Last Generated:{" "}
                                                    {
                                                        project.latest_report
                                                            .last_generated_at
                                                    }
                                                </p>
                                            </div>
                                        ) : (
                                            <p className="text-gray-500 italic">
                                                No report generated for this
                                                project yet. Click "Generate
                                                Reports Now" on the Dashboard.
                                            </p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
