import React, { useState } from "react";
import { Head } from "@inertiajs/react";
import { DragDropContext, Droppable, Draggable } from "@hello-pangea/dnd";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import CreateTaskModal from "@/Components/CreateTaskModal";

const getStatusColor = (status) => {
    switch (status) {
        case "pending":
            return "bg-blue-100 border-blue-300";
        case "in-progress":
            return "bg-yellow-100 border-yellow-300";
        case "done":
            return "bg-green-100 border-green-300";
        default:
            return "bg-gray-100 border-gray-300";
    }
};

const TaskCard = ({ task, index }) => (
    <Draggable draggableId={String(task.id)} index={index}>
        {(provided) => (
            <div
                ref={provided.innerRef}
                {...provided.draggableProps}
                {...provided.dragHandleProps}
                className={`p-3 mb-3 rounded-lg shadow-sm border ${getStatusColor(
                    task.status
                )}`}
            >
                <h5 className="font-semibold text-gray-800 text-md">
                    {task.title}
                </h5>
                {task.assignee && (
                    <p className="text-sm text-gray-600 mt-1">
                        Assigned to: {task.assignee.name}
                    </p>
                )}
                {task.due_date && (
                    <p className="text-xs text-gray-500 mt-1">
                        Due: {new Date(task.due_date).toLocaleDateString()}
                    </p>
                )}
                <p className="text-sm text-gray-700 mt-2 line-clamp-2">
                    {task.description}
                </p>
            </div>
        )}
    </Draggable>
);

const Column = ({ title, status, tasks, droppableId }) => (
    <div className="w-full md:w-1/3 p-4 bg-gray-50 rounded-lg shadow-md">
        <h4 className="text-xl font-semibold mb-4 border-b pb-2">
            {title} ({tasks.length})
        </h4>
        <Droppable droppableId={droppableId}>
            {(provided) => (
                <div
                    ref={provided.innerRef}
                    {...provided.droppableProps}
                    className="min-h-[200px] h-full"
                >
                    {tasks.length === 0 && (
                        <p className="text-gray-400 text-center py-8">
                            No tasks here yet.
                        </p>
                    )}
                    {tasks.map((task, index) => (
                        <TaskCard key={task.id} task={task} index={index} />
                    ))}
                    {provided.placeholder}
                </div>
            )}
        </Droppable>
    </div>
);

export default function KanbanBoard({
    auth,
    project,
    tasksPending,
    tasksInProgress,
    tasksDone,
    users,
}) {
    const [taskModal, setTaskModal] = useState(false);

    const [columns, setColumns] = useState({
        pending: tasksPending,
        "in-progress": tasksInProgress,
        done: tasksDone,
    });

    const onDragEnd = async (result) => {
        const { source, destination, draggableId } = result;

        if (!destination) {
            return;
        }

        if (
            source.droppableId === destination.droppableId &&
            source.index === destination.index
        ) {
            return;
        }

        const start = columns[source.droppableId];
        const end = columns[destination.droppableId];

        if (start === end) {
            const newTasks = Array.from(start);
            const [movedTask] = newTasks.splice(source.index, 1);
            newTasks.splice(destination.index, 0, movedTask);

            setColumns({
                ...columns,
                [source.droppableId]: newTasks,
            });
        } else {
            const startTasks = Array.from(start);
            const [movedTask] = startTasks.splice(source.index, 1);
            const endTasks = Array.from(end);
            endTasks.splice(destination.index, 0, {
                ...movedTask,
                status: destination.droppableId,
            });

            setColumns({
                ...columns,
                [source.droppableId]: startTasks,
                [destination.droppableId]: endTasks,
            });

            try {
                await axios.put(route("tasks.updateStatus", draggableId), {
                    status: destination.droppableId,
                });
                console.log(
                    `Task ${draggableId} moved to ${destination.droppableId}`
                );
            } catch (error) {
                console.error("Error updating task status:", error);
            }
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Kanban Board
                </h2>
            }
        >
            <Head title={`Kanban: ${project.name}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-2xl font-bold mb-6">
                                {project.name}
                            </h3>
                            <div className="flex justify-between">
                                <p className="mb-8 text-gray-700">
                                    {project.description}
                                </p>
                                <div className="flex items-end md:col-span-2 mb-8">
                                    <button
                                        type="submit"
                                        className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        onClick={() => setTaskModal(true)}
                                    >
                                        Create Task
                                    </button>
                                </div>
                            </div>

                            <DragDropContext onDragEnd={onDragEnd}>
                                <div className="flex flex-col md:flex-row gap-6">
                                    <Column
                                        title="Pending"
                                        status="pending"
                                        tasks={columns.pending}
                                        droppableId="pending"
                                    />
                                    <Column
                                        title="In Progress"
                                        status="in-progress"
                                        tasks={columns["in-progress"]}
                                        droppableId="in-progress"
                                    />
                                    <Column
                                        title="Done"
                                        status="done"
                                        tasks={columns.done}
                                        droppableId="done"
                                    />
                                </div>
                            </DragDropContext>
                        </div>
                    </div>
                </div>
                <CreateTaskModal
                    isCreateTaskModalOpen={taskModal}
                    setIsCreateTaskModalOpen={setTaskModal}
                    project={project}
                    users={users}
                />
            </div>
        </AuthenticatedLayout>
    );
}
