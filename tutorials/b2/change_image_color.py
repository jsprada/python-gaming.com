import pygame as pg

pg.init()

def colorize(image, newColor):
    """
    Create a "colorized" copy of a surface (replaces RGB values with the given color, preserving the per-pixel alphas of
    original).
    :param image: Surface to create a colorized copy of
    :param newColor: RGB color to use (original alpha values are preserved)
    :return: New colorized Surface instance
    """
    image = image.copy()

    # zero out RGB values
    image.fill((0, 0, 0, 255), None, pg.BLEND_RGBA_MULT)
    # add in new RGB values
    image.fill(newColor[0:3] + (0,), None, pg.BLEND_RGBA_ADD)

    return image

class Player:
    def __init__(self, screen_rect):
        self.image = pg.image.load('chevron.png').convert_alpha()
        self.image = colorize(self.image, (255,0,0))
        self.rect = self.image.get_rect(center=screen_rect.center)

    def draw(self, surf):
        surf.blit(self.image, self.rect)

screen = pg.display.set_mode((800,600))
screen_rect = screen.get_rect()
player = Player(screen_rect)
done = False
while not done:
    screen.fill((255,255,255))
    for event in pg.event.get():
        if event.type == pg.QUIT:
            done = True
    player.draw(screen)
    pg.display.update()
