"use client";
import React, { useEffect, useState } from "react";
import Box from "@mui/material/Box";
import Modal from "@mui/material/Modal";
import { Close } from "@mui/icons-material";
import { Head, useForm } from "@inertiajs/react";

const modalStyle = {
    position: "absolute",
    top: "50%",
    left: "50%",
    transform: "translate(-50%, -50%)",
    p: 4,
};

export default function CreateTaskModal({
    isCreateTaskModalOpen,
    setIsCreateTaskModalOpen,
    project,
    users,
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        project_id: project?.id || "",
        title: "",
        description: "",
        status: "pending",
        assigned_to: "",
        due_date: "",
    });

    const submitTask = (e) => {
        e.preventDefault();
        post(route("tasks.store"), {
            preserveScroll: true,
            onSuccess: (response) => {
                alert("Task added!");
                reset();
                window.location.reload();
            },
            onError: () => alert("Failed to add task."),
        });
    };

    return (
        <div>
            <Modal
                open={isCreateTaskModalOpen}
                onClose={(event, reason) => {
                    if (reason !== "backdropClick") {
                        setIsCreateTaskModalOpen(false);
                    }
                }}
                aria-labelledby="modal-modal-title"
                aria-describedby="modal-modal-description"
            >
                <Box
                    sx={modalStyle}
                    className="bg-[#FBFBFB] border-none rounded-lg shadow-lg w-[90%] max-w-2xl h-auto px-6 py-4"
                >
                    <div className="flex justify-end mb-2 cursor-pointer"
                    onClick={()=> setIsCreateTaskModalOpen(false)}>
                        <Close />
                    </div>
                    <div className="mb-8 p-4 bg-gray-50 rounded-lg shadow-sm">
                        <h4 className="text-lg font-medium mb-4">
                            Add New Task
                        </h4>
                        <form
                            onSubmit={submitTask}
                            className="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div>
                                <label
                                    htmlFor="title"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Title
                                </label>
                                <input
                                    id="title"
                                    type="text"
                                    value={data.title}
                                    onChange={(e) =>
                                        setData("title", e.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                />
                                {errors.title && (
                                    <p className="text-red-500 text-xs mt-1">
                                        {errors.title}
                                    </p>
                                )}
                            </div>
                            <div>
                                <label
                                    htmlFor="assigned_to"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Assignee
                                </label>
                                <select
                                    id="assigned_to"
                                    value={data.assigned_to}
                                    onChange={(e) =>
                                        setData("assigned_to", e.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="">Unassigned</option>
                                    {users.map((user) => (
                                        <option key={user.id} value={user.id}>
                                            {user.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.assigned_to && (
                                    <p className="text-red-500 text-xs mt-1">
                                        {errors.assigned_to}
                                    </p>
                                )}
                            </div>
                            <div className="md:col-span-2">
                                <label
                                    htmlFor="description"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) =>
                                        setData("description", e.target.value)
                                    }
                                    rows="3"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                ></textarea>
                                {errors.description && (
                                    <p className="text-red-500 text-xs mt-1">
                                        {errors.description}
                                    </p>
                                )}
                            </div>
                            <div>
                                <label
                                    htmlFor="due_date"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Due Date
                                </label>
                                <input
                                    id="due_date"
                                    type="date"
                                    value={data.due_date}
                                    onChange={(e) =>
                                        setData("due_date", e.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                                {errors.due_date && (
                                    <p className="text-red-500 text-xs mt-1">
                                        {errors.due_date}
                                    </p>
                                )}
                            </div>
                            <div className="flex items-end md:col-span-2">
                                <button
                                    type="submit"
                                    className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    disabled={processing}
                                >
                                    {processing ? "Adding..." : "Add Task"}
                                </button>
                            </div>
                        </form>
                    </div>
                </Box>
            </Modal>
        </div>
    );
}
