// Animation variants for different components
export const fadeIn = {
  initial: { opacity: 0 },
  enter: { opacity: 1 },
  exit: { opacity: 0 },
};

export const slideUp = {
  initial: { opacity: 0, y: 20 },
  enter: { opacity: 1, y: 0 },
  exit: { opacity: 0, y: -20 },
};

export const slideInFromLeft = {
  initial: { opacity: 0, x: -20 },
  enter: { opacity: 1, x: 0 },
  exit: { opacity: 0, x: 20 },
};

export const scaleIn = {
  initial: { opacity: 0, scale: 0.9 },
  enter: { opacity: 1, scale: 1 },
  exit: { opacity: 0, scale: 0.9 },
};

export const staggerChildren = {
  enter: {
    transition: {
      staggerChildren: 0.1,
      delayChildren: 0.2,
    },
  },
};

export const hoverScale = {
  scale: 1.05,
  transition: { duration: 0.2 },
};

export const tapScale = {
  scale: 0.95,
};

// Transition presets
export const transitions = {
  smooth: { duration: 0.3, ease: "easeInOut" },
  spring: { type: "spring", stiffness: 300, damping: 25 },
  bounce: { type: "spring", stiffness: 400, damping: 15 },
};
