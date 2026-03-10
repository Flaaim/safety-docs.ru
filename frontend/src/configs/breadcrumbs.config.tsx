
export interface BreadcrumbItem {
  title: string;
  link: string;
}

export const breadcrumbsConfig: Record<string, BreadcrumbItem[]> = {
  "/safety": [
    { title: "Охрана труда", link: "/safety" }
  ],
  "/safety/service": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Служба охраны труда", link: "/safety/service" }
  ],
  "/safety/suot": [
    { title: "Охрана труда", link: "/safety" },
    { title: "СУОТ", link: "/safety/suot" }
  ],
  "/safety/education": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Обучение охраны труда", link: "/safety/education" }
  ],
  "/safety/medical": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Медосмотры", link: "/safety/medical" }
  ],
  "/safety/firstaid": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Аптечки", link: "/safety/firstaid" }
  ],
  "/safety/rules": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Инструкции по охране труда", link: "/safety/rules" }
  ],
  "/safety/control": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Контроль требований охраны труда", link: "/safety/control" }
  ],
  "/safety/electrical": [
    { title: "Охрана труда", link: "/safety" },
    { title: "Электробезопасность", link: "/safety/electrical" }
  ]
}
