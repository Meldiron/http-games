import type { BaseLayoutProps } from "fumadocs-ui/layouts/shared";

export function baseOptions(): BaseLayoutProps {
  return {
    githubUrl: "https://github.com/meldiron/http-games",
    nav: {
      title: <>
        <img src="/logo.svg" alt="Logo" className="h-5 hidden dark:block" />
        <img src="/logo-dark.svg" alt="Logo" className="h-5 block dark:hidden" />
        HTTP Games
      </>,
    },
  };
}
