import pygame as pg

pg.init()

class Player:
    def __init__(self, screen_rect):
        self.image = pg.image.load('spaceship.png').convert()
        self.image.set_colorkey((255,0,255))
        self.rect = self.image.get_rect(center=screen_rect.center)

    def draw(self, surf):
        surf.blit(self.image, self.rect)

screen = pg.display.set_mode((800,600))
screen_rect = screen.get_rect()
player = Player(screen_rect)
done = False
while not done:
    for event in pg.event.get():
        if event.type == pg.QUIT:
            done = True
    player.draw(screen)
    pg.display.update()
